<?php

namespace App\Http\Controllers;

use App\Exports\AccountPayableExport;
use App\Exports\AccountReceivableDetailExport;
use App\Http\Requests\AccountPayableCreateRequest;
use App\Http\Requests\AccountPayableUpdateRequest;
use App\Models\AccountPayable;
use App\Models\Customer;
use App\Models\Supplier;
use App\Utilities\Constant;
use App\Utilities\Services\AccountPayableService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AccountPayableController extends Controller
{
    public function index(Request $request) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->subDays(90)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $accountPayableStatuses = Constant::ACCOUNT_PAYABLE_STATUSES;
        $status = $accountPayableStatuses;

        if(!empty($filter->status)) {
            $status = [$filter->status];
        }

        $baseQuery = AccountPayableService::getBaseQueryIndex();

        $accountPayables = $baseQuery
            ->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('goods_receipts.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->orderBy('suppliers.name')
            ->get();

        foreach($accountPayables as $accountPayable) {
            $paymentAmount = $accountPayable->payment_amount ?? 0;
            $returnAmount = $accountPayable->return_amount ?? 0;

            $outstandingAmount = $accountPayable->grand_total - $paymentAmount - $returnAmount;
            $payableStatus = Constant::ACCOUNT_PAYABLE_STATUS_UNPAID;

            if($outstandingAmount <= 0) {
                $payableStatus = Constant::ACCOUNT_PAYABLE_STATUS_PAID;
            } else if($paymentAmount > 0 || $returnAmount > 0) {
                $payableStatus = Constant::ACCOUNT_PAYABLE_STATUS_ONGOING;
            }

            $accountPayable->outstanding_amount = $outstandingAmount;
            $accountPayable->status = $payableStatus;
        }

        $accountPayables = $accountPayables->filter(function ($item) use ($status) {
            return in_array($item->status, $status);
        });

        $data = [
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'accountPayableStatuses' => $accountPayableStatuses,
            'status' => $filter->status ?? 0,
            'accountPayables' => $accountPayables
        ];

        return view('pages.finance.account-payable.index', $data);
    }

    public function detail(Request $request, $id) {
        $filter = (object) $request->all();

        $startDate = $filter->start_date ?? Carbon::now()->subDays(90)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $accountPayableStatuses = Constant::ACCOUNT_PAYABLE_STATUSES;
        $status = $accountPayableStatuses;

        if(!empty($filter->status)) {
            $status = [$filter->status];
        }

        $supplier = Supplier::query()->findOrFail($id);
        $baseQuery = AccountPayableService::getBaseQueryDetail();

        $accountPayables = $baseQuery
            ->where('goods_receipts.supplier_id', $id)
            ->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('goods_receipts.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->whereIn('account_payables.status', $status)
            ->orderByDesc('goods_receipts.date')
            ->orderBy('goods_receipts.id')
            ->get();

        foreach($accountPayables as $accountPayable) {
            $paymentAmount = $accountPayable->payment_amount ?? 0;
            $returnAmount = $accountPayable->return_amount ?? 0;
            $outstandingAmount = $accountPayable->grand_total - $paymentAmount - $returnAmount;

            $accountPayable->outstanding_amount = $outstandingAmount;
        }

        $data = [
            'id' => $id,
            'startDate' => $startDate,
            'finalDate' => $finalDate,
            'accountPayableStatuses' => $accountPayableStatuses,
            'status' => $filter->status ?? null,
            'accountPayables' => $accountPayables,
            'supplier' => $supplier
        ];

        return view('pages.finance.account-payable.detail', $data);
    }

    public function payment($id) {
        $baseQuery = AccountPayableService::getBaseQueryDetail();
        $accountPayable = $baseQuery->where('account_payables.id', $id)->first();

        $grandTotal = $accountPayable->grand_total;
        $paymentAmount = $accountPayable->payment_amount ?? 0;
        $outstandingAmount = $grandTotal - $paymentAmount;
        $accountPayable->outstanding_amount = $outstandingAmount;

        $accountPayablePayments = $accountPayable->payments;
        foreach($accountPayablePayments as $payment) {
            $payment->outstanding_amount = $grandTotal - $payment->amount;
            $grandTotal -= $payment->amount;
        }

        $rowNumbers = $accountPayablePayments->count();

        $data = [
            'accountPayable' => $accountPayable,
            'accountPayablePayments' => $accountPayablePayments,
            'rowNumbers' => $rowNumbers
        ];

        return view('pages.finance.account-payable.payment', $data);
    }

    public function store(AccountPayableCreateRequest $request) {
        try {
            DB::beginTransaction();

            $payableId = $request->get('payable_id') ?? 0;
            $accountPayable = AccountPayable::query()->findOrFail($payableId);

            $accountPayable->payments()->delete();

            $totalPayment = 0;
            $paymentDates = $request->get('payment_date', []);
            foreach ($paymentDates as $index => $paymentDate) {
                if(!empty($paymentDate)) {
                    $date = Carbon::createFromFormat('d-m-Y', $paymentDate)->format('Y-m-d');
                    $paymentAmount = $request->get('payment_amount')[$index];

                    $accountPayable->payments()->create([
                        'date' => $date,
                        'amount' => $paymentAmount,
                    ]);

                    $totalPayment += $paymentAmount;
                }
            }

            $status = Constant::ACCOUNT_PAYABLE_STATUS_ONGOING;
            if($totalPayment == $accountPayable->goodsReceipt->grand_total) {
                $status = Constant::ACCOUNT_PAYABLE_STATUS_PAID;
            }

            $accountPayable->update([
                'status' => $status,
            ]);

            DB::commit();

            return redirect()->route('account-payables.detail', ['id' => $accountPayable->goodsReceipt->supplier_id]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'message' => 'An error occurred while saving data'
            ]);
        }
    }

    public function return($id) {
        $baseQuery = AccountPayableService::getBaseQueryDetail();
        $accountPayable = $baseQuery->where('account_payables.id', $id)->first();
        $accountPayableReturns = $accountPayable->returns;

        $rowNumbers = $accountPayableReturns->count();

        $data = [
            'accountPayable' => $accountPayable,
            'accountPayableReturns' => $accountPayableReturns,
            'prices' => $prices ?? [],
            'rowNumbers' => $rowNumbers
        ];

        return view('pages.finance.account-payable.return', $data);
    }

    public function update(AccountPayableUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $accountPayable = AccountPayable::query()->findOrFail($id);
            $accountPayable->returns()->delete();

            $totalReturn = 0;
            $productIds = $request->get('product_id', []);
            foreach ($productIds as $index => $productId) {
                $purchaseReturnId = $request->get('purchase_return_id')[$index];
                $unitId = $request->get('unit_id')[$index];
                $quantity = $request->get('quantity')[$index];
                $realQuantity = $request->get('real_quantity')[$index];
                $price = $request->get('price')[$index];
                $wages = $request->get('wages')[$index];
                $shippingCost = $request->get('shipping_cost')[$index];

                $actualQuantity = $quantity * $realQuantity;
                $totalExpenses = $wages + $shippingCost;
                $total = ($quantity * $price) + $totalExpenses;

                $accountPayable->returns()->create([
                    'product_id' => $productId,
                    'purchase_return_id' => $purchaseReturnId,
                    'unit_id' => $unitId,
                    'quantity' => $quantity,
                    'actual_quantity' => $actualQuantity,
                    'price' => $price,
                    'wages' => $wages,
                    'shipping_cost' => $shippingCost,
                    'total' => $total,
                ]);

                $totalReturn += $total;
            }

            $totalPayment = $accountPayable->payments()->sum('amount') ?? 0;
            $grandTotal = $accountPayable->goodsReceipt->grand_total;

            $status = $accountPayable->status;
            if($grandTotal == ($totalReturn + $totalPayment)) {
                $status = Constant::ACCOUNT_PAYABLE_STATUS_PAID;
            } elseif($totalPayment > 0 || $totalReturn > 0) {
                $status = Constant::ACCOUNT_PAYABLE_STATUS_ONGOING;
            }

            $accountPayable->update([
                'status' => $status,
            ]);

            DB::commit();

            return redirect()->route('account-payables.detail', ['id' => $accountPayable->goodsReceipt->supplier_id]);
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

        return Excel::download(new AccountPayableExport($request), 'Payable_Data_'.$fileDate.'.xlsx');
    }

    public function exportDetail(Request $request, $id) {
        $customer = Customer::query()->findOrFail($id);
        $customerName = preg_replace('/\s+/', '_', $customer->name);

        $fileDate = Carbon::now()->format('Y_m_d');

        return Excel::download(new AccountReceivableDetailExport($id, $request), 'Receivable_'.$customerName.'_'.$fileDate.'.xlsx');
    }
}
