<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountPayableCreateRequest;
use App\Http\Requests\GoodsReceiptCancelRequest;
use App\Http\Requests\GoodsReceiptUpdateRequest;
use App\Models\AccountPayable;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\Supplier;
use App\Utilities\Constant;
use App\Utilities\Services\AccountPayableService;
use App\Utilities\Services\ApprovalService;
use App\Utilities\Services\GoodsReceiptService;
use App\Utilities\Services\ProductService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $outstandingAmount = $accountPayable->grand_total - $paymentAmount;
            $payableStatus = Constant::ACCOUNT_PAYABLE_STATUS_UNPAID;

            if($outstandingAmount <= 0) {
                $payableStatus = Constant::ACCOUNT_PAYABLE_STATUS_PAID;
            } else if($paymentAmount > 0) {
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
            $outstandingAmount = $accountPayable->grand_total - $paymentAmount;

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

    public function edit($id) {
        $goodsReceipt = GoodsReceipt::query()->findOrFail($id);
        $goodsReceiptItems = $goodsReceipt->goodsReceiptItems;

        if(isWaitingApproval($goodsReceipt->status) && isApprovalTypeEdit($goodsReceipt->pendingApproval->type)) {
            $goodsReceipt = GoodsReceiptService::mapGoodsReceiptApproval($goodsReceipt);
            $goodsReceiptItems = $goodsReceipt->goodsReceiptItems;
        }

        $products = Product::all();
        $rowNumbers = count($goodsReceiptItems);

        $productIds = $goodsReceiptItems->pluck('product_id')->toArray();
        $productConversions = ProductService::findProductConversions($productIds);

        foreach($goodsReceiptItems as $goodsReceiptItem) {
            $units[$goodsReceiptItem->product_id][] = [
                'id' => $goodsReceiptItem->product->unit_id,
                'name' => $goodsReceiptItem->product->unit->name,
                'quantity' => 1
            ];
        }

        foreach($productConversions as $conversion) {
            $units[$conversion->product_id][] = [
                'id' => $conversion->unit_id,
                'name' => $conversion->unit->name,
                'quantity' => $conversion->quantity
            ];
        }

        $data = [
            'id' => $id,
            'goodsReceipt' => $goodsReceipt,
            'goodsReceiptItems' => $goodsReceiptItems,
            'products' => $products,
            'rowNumbers' => $rowNumbers,
            'units' => $units ?? [],
        ];

        return view('pages.admin.goods-receipt.edit', $data);
    }

    public function update(GoodsReceiptUpdateRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $goodsReceipt = GoodsReceipt::query()->findOrFail($id);
            $goodsReceipt->update([
                'status' => Constant::GOODS_RECEIPT_STATUS_WAITING_APPROVAL
            ]);

            ApprovalService::deleteData($goodsReceipt->approvals);

            $parentApproval = ApprovalService::createData(
                $goodsReceipt,
                $goodsReceipt->goodsReceiptItems,
                Constant::APPROVAL_TYPE_EDIT,
                Constant::APPROVAL_STATUS_PENDING,
                $request->get('description', '')
            );

            ApprovalService::createData(
                $goodsReceipt,
                $data,
                Constant::APPROVAL_TYPE_EDIT,
                Constant::APPROVAL_STATUS_PENDING,
                $data['description'],
                $parentApproval->id
            );

            DB::commit();

            return redirect()->route('goods-receipts.index-edit');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('goods-receipts.edit', $id)->withInput()->withErrors([
                'message' => 'An error occurred while updating data'
            ]);
        }
    }
}
