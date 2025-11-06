<?php

namespace App\Http\Controllers;

use App\Exports\AccountReceivableDetailExport;
use App\Exports\AccountReceivableExport;
use App\Http\Requests\AccountReceivableCreateRequest;
use App\Http\Requests\AccountReceivableUpdateRequest;
use App\Models\AccountReceivable;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Utilities\Constant;
use App\Utilities\Services\AccountReceivableService;
use App\Utilities\Services\ProductService;
use App\Utilities\Services\SalesOrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AccountReceivableController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->subDays(90)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');

        $accountReceivables = AccountReceivableService::getIndexData($filter);

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'accountReceivableStatuses' => Constant::ACCOUNT_RECEIVABLE_STATUSES,
            'status' => $filter->status ?? 0,
            'accountReceivables' => $accountReceivables
        ];

        return view('pages.finance.account-receivable.index', $data);
    }

    public function detail(Request $request, $id) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->subDays(90)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $accountReceivableStatuses = Constant::ACCOUNT_RECEIVABLE_STATUSES;

        $customer = Customer::query()->findOrFail($id);
        $accountReceivables = AccountReceivableService::getDetailData($id, $filter);

        $data = [
            'id' => $id,
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'accountReceivableStatuses' => $accountReceivableStatuses,
            'status' => $filter->status ?? null,
            'accountReceivables' => $accountReceivables,
            'customer' => $customer
        ];

        return view('pages.finance.account-receivable.detail', $data);
    }

    public function payment($id) {
        $baseQuery = AccountReceivableService::getBaseQueryDetail();
        $accountReceivable = $baseQuery->where('account_receivables.id', $id)->first();

        $grandTotal = $accountReceivable->grand_total;
        $paymentAmount = $accountReceivable->payment_amount ?? 0;
        $returnAmount = $accountReceivable->return_amount ?? 0;

        $outstandingAmount = $grandTotal - $returnAmount;
        $finalOutstandingAmount = $grandTotal - $paymentAmount - $returnAmount;

        $accountReceivable->outstanding_amount = $outstandingAmount;
        $accountReceivable->final_outstanding_amount = $finalOutstandingAmount;

        $accountReceivablePayments = $accountReceivable->payments;
        foreach($accountReceivablePayments as $payment) {
            $payment->outstanding_amount = $outstandingAmount - $payment->amount;
            $outstandingAmount -= $payment->amount;
        }

        $rowNumbers = $accountReceivablePayments->count();

        $data = [
            'accountReceivable' => $accountReceivable,
            'accountReceivablePayments' => $accountReceivablePayments,
            'rowNumbers' => $rowNumbers
        ];

        return view('pages.finance.account-receivable.payment', $data);
    }

    public function store(AccountReceivableCreateRequest $request) {
        try {
            DB::beginTransaction();

            $receivableId = $request->get('receivable_id') ?? 0;
            $accountReceivable = AccountReceivable::query()->findOrFail($receivableId);

            $accountReceivable->payments()->delete();

            $totalPayment = 0;
            $paymentDates = $request->get('payment_date', []);
            foreach ($paymentDates as $index => $paymentDate) {
                if(!empty($paymentDate)) {
                    $date = Carbon::createFromFormat('d-m-Y', $paymentDate)->format('Y-m-d');
                    $paymentAmount = $request->get('payment_amount')[$index];

                    $accountReceivable->payments()->create([
                        'date' => $date,
                        'amount' => $paymentAmount,
                    ]);

                    $totalPayment += $paymentAmount;
                }
            }

            $returnAmount = $accountReceivable->returns()->sum('final_amount') ?? 0;

            $status = Constant::ACCOUNT_RECEIVABLE_STATUS_ONGOING;
            if($totalPayment == ($accountReceivable->salesOrder->grand_total - $returnAmount)) {
                $status = Constant::ACCOUNT_RECEIVABLE_STATUS_PAID;
            }

            $accountReceivable->update([
                'status' => $status,
            ]);

            DB::commit();

            return redirect()->route('account-receivables.detail', ['id' => $accountReceivable->salesOrder->customer_id]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function return($id) {
        $baseQuery = AccountReceivableService::getBaseQueryDetail();
        $accountReceivable = $baseQuery->where('account_receivables.id', $id)->first();
        $accountReceivableReturns = $accountReceivable->returns;

        $productIds = $accountReceivableReturns->pluck('product_id')->toArray();
        $productPrices = ProductService::findProductPrices($productIds);

        foreach($productPrices as $productPrice) {
            $prices[$productPrice->product_id][] = [
                'id' => $productPrice->price_id,
                'code' => $productPrice->pricing->code,
                'price' => $productPrice->price
            ];
        }

        $rowNumbers = $accountReceivableReturns->count();

        $data = [
            'accountReceivable' => $accountReceivable,
            'accountReceivableReturns' => $accountReceivableReturns,
            'prices' => $prices ?? [],
            'rowNumbers' => $rowNumbers
        ];

        return view('pages.finance.account-receivable.return', $data);
    }

    public function update(AccountReceivableUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $accountReceivable = AccountReceivable::query()->findOrFail($id);
            $accountReceivable->returns()->delete();

            $totalReturn = 0;
            $productIds = $request->get('product_id', []);
            foreach ($productIds as $index => $productId) {
                $salesReturnId = $request->get('sales_return_id')[$index];
                $unitId = $request->get('unit_id')[$index];
                $quantity = $request->get('quantity')[$index];
                $realQuantity = $request->get('real_quantity')[$index];
                $priceId = $request->get('price_id')[$index];
                $price = $request->get('price')[$index];
                $discount = $request->get('discount')[$index];
                $discountAmount = $request->get('discount_product')[$index];

                $actualQuantity = $quantity * $realQuantity;
                $total = $quantity * $price;
                $finalAmount = $total - $discountAmount;

                $accountReceivable->returns()->create([
                    'product_id' => $productId,
                    'sales_return_id' => $salesReturnId,
                    'unit_id' => $unitId,
                    'quantity' => $quantity,
                    'actual_quantity' => $actualQuantity,
                    'price_id' => $priceId,
                    'price' => $price,
                    'total' => $total,
                    'discount' => $discount,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $finalAmount
                ]);

                $totalReturn += $finalAmount;
            }

            $totalPayment = $accountReceivable->payments()->sum('amount') ?? 0;
            $grandTotal = $accountReceivable->salesOrder->grand_total;

            $status = $accountReceivable->status;
            if($grandTotal == ($totalReturn + $totalPayment)) {
                $status = Constant::ACCOUNT_RECEIVABLE_STATUS_PAID;
            } elseif($totalPayment > 0 || $totalReturn > 0) {
                $status = Constant::ACCOUNT_RECEIVABLE_STATUS_ONGOING;
            }

            $accountReceivable->update([
                'status' => $status,
            ]);

            DB::commit();

            return redirect()->route('account-receivables.detail', ['id' => $accountReceivable->salesOrder->customer_id]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }

    public function export(Request $request) {
        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new AccountReceivableExport($request), 'Receivable_Data_'.$fileDate.'.xlsx');
    }

    public function pdf(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->subDays(90)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');

        $accountReceivables = AccountReceivableService::getIndexData($filter);

        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');
        $fileDate = Carbon::now()->format('Y_m_d');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'accountReceivables' => $accountReceivables,
            'exportDate' => $exportDate,
        ];

        $pdf = PDF::loadview('pages.finance.account-receivable.pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Receivable_Data_'.$fileDate.'.pdf');
    }

    public function exportDetail(Request $request, $id) {
        $customer = Customer::query()->findOrFail($id);
        $customerName = preg_replace('/\s+/', '_', $customer->name);

        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new AccountReceivableDetailExport($id, $request), 'Receivable_'.$customerName.'_'.$fileDate.'.xlsx');
    }

    public function pdfDetail(Request $request, $id) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->subDays(90)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');

        $accountReceivables = AccountReceivableService::getDetailData($id, $filter);

        $customer = Customer::query()->findOrFail($id);
        $customerName = preg_replace('/\s+/', '_', $customer->name);

        $exportDate = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm:ss');
        $fileDate = Carbon::now()->format('Y_m_d');

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'accountReceivables' => $accountReceivables,
            'customer' => $customer,
            'exportDate' => $exportDate,
        ];

        $pdf = PDF::loadview('pages.finance.account-receivable.pdf-detail', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Receivable_'.$customerName.'_'.$fileDate.'.pdf');
    }

    public function checkInvoice(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? null;
        $finalDate = $filter->final_date ?? null;
        $number = $filter->number ?? null;
        $customerId = $filter->customer_id ?? null;

        if(!$number && !$customerId && !$startDate && !$finalDate) {
            $startDate = Carbon::now()->format('d-m-Y');
            $finalDate = Carbon::now()->format('d-m-Y');
        }

        $customers = Customer::all();
        $baseQuery = SalesOrderService::getBaseQueryIndex();

        if($startDate) {
            $baseQuery = $baseQuery->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay());
        }

        if($finalDate) {
            $baseQuery = $baseQuery->where('sales_orders.date', '<=', Carbon::parse($finalDate)->endOfDay());
        }

        if($number) {
            $baseQuery = $baseQuery->where('sales_orders.number', $number);
        }

        if($customerId) {
            $baseQuery = $baseQuery->where('sales_orders.customer_id', $customerId);
        }

        $salesOrders = $baseQuery
            ->orderByDesc('sales_orders.date')
            ->orderByDesc('sales_orders.id')
            ->get();

        $salesOrders = SalesOrderService::mapSalesOrderIndex($salesOrders, true);

        $productWarehouses = [];
        foreach ($salesOrders as $salesOrder) {
            foreach($salesOrder->salesOrderItems as $salesOrderItem) {
                $productWarehouses[$salesOrder->id][$salesOrderItem->product_id][$salesOrderItem->warehouse_id] = $salesOrderItem->quantity;
            }
        }

        foreach ($salesOrders as $salesOrder) {
            $salesOrder->salesOrderItems = SalesOrderService::mapSalesOrderItemDetail($salesOrder->salesOrderItems);
        }

        $warehouses = Warehouse::query()
            ->where('type', '!=', Constant::WAREHOUSE_TYPE_RETURN)
            ->get();

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'number' => $number,
            'customerId' => $customerId,
            'customers' => $customers,
            'salesOrders' => $salesOrders,
            'productWarehouses' => $productWarehouses,
            'warehouses' => $warehouses,
            'totalWarehouses' => $warehouses->count(),
        ];

        return view('pages.finance.account-receivable.check-invoice', $data);
    }
}
