<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountReceivableCreateRequest;
use App\Models\AccountReceivable;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Utilities\Constant;
use App\Utilities\Services\AccountReceivableService;
use App\Utilities\Services\SalesOrderService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountReceivableController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->subDays(90)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $accountReceivableStatuses = Constant::ACCOUNT_RECEIVABLE_STATUSES;
        $status = $accountReceivableStatuses;

        if(!empty($filter->status)) {
            $status = [$filter->status];
        }

        $baseQuery = AccountReceivableService::getBaseQueryIndex();

        $accountReceivables = $baseQuery
            ->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('sales_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->orderBy('customers.name')
            ->get();

        foreach($accountReceivables as $accountReceivable) {
            $paymentAmount = $accountReceivable->payment_amount ?? 0;
            $returnAmount = $accountReceivable->return_amount ?? 0;

            $outstandingAmount = $accountReceivable->grand_total - $paymentAmount - $returnAmount;
            $receivableStatus = Constant::ACCOUNT_RECEIVABLE_STATUS_UNPAID;

            if($outstandingAmount <= 0) {
                $receivableStatus = Constant::ACCOUNT_RECEIVABLE_STATUS_PAID;
            } else if($paymentAmount > 0) {
                $receivableStatus = Constant::ACCOUNT_RECEIVABLE_STATUS_ONGOING;
            }

            $accountReceivable->outstanding_amount = $outstandingAmount;
            $accountReceivable->status = $receivableStatus;
        }

        $accountReceivables = $accountReceivables->filter(function ($item) use ($status) {
            return in_array($item->status, $status);
        });

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'accountReceivableStatuses' => $accountReceivableStatuses,
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
        $status = $accountReceivableStatuses;

        if(!empty($filter->status)) {
            $status = [$filter->status];
        }

        $customer = Customer::query()->findOrFail($id);
        $baseQuery = AccountReceivableService::getBaseQueryDetail();

        $accountReceivables = $baseQuery
            ->where('sales_orders.customer_id', $id)
            ->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('sales_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->whereIn('account_receivables.status', $status)
            ->orderByDesc('sales_orders.date')
            ->orderBy('sales_orders.id')
            ->get();

        foreach($accountReceivables as $accountReceivable) {
            $paymentAmount = $accountReceivable->payment_amount ?? 0;
            $returnAmount = $accountReceivable->return_amount ?? 0;
            $outstandingAmount = $accountReceivable->grand_total - $paymentAmount - $returnAmount;

            $accountReceivable->outstanding_amount = $outstandingAmount;
        }

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

            $payableId = $request->get('receivable_id') ?? 0;
            $accountReceivable = AccountReceivable::query()->findOrFail($payableId);

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
