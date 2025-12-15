<?php

namespace App\Utilities\Services;

use App\Models\AccountPayable;
use App\Utilities\Constant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccountPayableService
{
    public static function getBaseQueryIndex() {
        return AccountPayable::query()
            ->select(
                'goods_receipts.supplier_id',
                'suppliers.name AS supplier_name',
                DB::raw('SUM(grand_total) AS grand_total'),
                DB::raw('COUNT(account_payables.id) AS invoice_count'),
                DB::raw('SUM(payments.total_payment) AS payment_amount'),
                DB::raw('SUM(returns.total_return) AS return_amount')
            )
            ->join('goods_receipts', 'goods_receipts.id', 'account_payables.goods_receipt_id')
            ->join('suppliers', 'suppliers.id', 'goods_receipts.supplier_id')
            ->leftJoinSub(
                DB::table('account_payable_payments')
                    ->select(
                        'account_payable_payments.account_payable_id',
                        DB::raw('SUM(account_payable_payments.amount) AS total_payment')
                    )
                    ->whereNull('account_payable_payments.deleted_at')
                    ->groupBy('account_payable_payments.account_payable_id'),
                'payments',
                'payments.account_payable_id',
                'account_payables.id'
            )
            ->leftJoinSub(
                DB::table('account_payable_returns')
                    ->select(
                        'account_payable_returns.account_payable_id',
                        DB::raw('SUM(account_payable_returns.total) AS total_return')
                    )
                    ->whereNull('account_payable_returns.deleted_at')
                    ->groupBy('account_payable_returns.account_payable_id'),
                'returns',
                'returns.account_payable_id',
                'account_payables.id'
            )
            ->groupBy('goods_receipts.supplier_id');
    }

    public static function getBaseQueryDetail() {
        return AccountPayable::query()
            ->select(
                'account_payables.*',
                'goods_receipts.number',
                'goods_receipts.date',
                'goods_receipts.supplier_id',
                'goods_receipts.tempo',
                'goods_receipts.grand_total',
                'branches.name AS branch_name',
                'suppliers.name AS supplier_name',
                DB::raw('SUM(payments.total_payment) AS payment_amount'),
                DB::raw('SUM(returns.total_return) AS return_amount'),
                DB::raw('SUM(returns.total_quantity) AS total_quantity'),
            )
            ->join('goods_receipts', 'goods_receipts.id', 'account_payables.goods_receipt_id')
            ->join('branches', 'branches.id', 'goods_receipts.branch_id')
            ->join('suppliers', 'suppliers.id', 'goods_receipts.supplier_id')
            ->leftJoinSub(
                DB::table('account_payable_payments')
                    ->select(
                        'account_payable_payments.account_payable_id',
                        DB::raw('SUM(account_payable_payments.amount) AS total_payment')
                    )
                    ->whereNull('account_payable_payments.deleted_at')
                    ->groupBy('account_payable_payments.account_payable_id'),
                'payments',
                'payments.account_payable_id',
                'account_payables.id'
            )
            ->leftJoinSub(
                DB::table('account_payable_returns')
                    ->select(
                        'account_payable_returns.account_payable_id',
                        DB::raw('SUM(account_payable_returns.total) AS total_return'),
                        DB::raw('SUM(account_payable_returns.quantity) AS total_quantity')
                    )
                    ->whereNull('account_payable_returns.deleted_at')
                    ->groupBy('account_payable_returns.account_payable_id'),
                'returns',
                'returns.account_payable_id',
                'account_payables.id'
            )
            ->groupBy('account_payables.id');
    }

    public static function getIndexData($filter) {
        $startDate = $filter->start_date ?? Carbon::now()->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $supplierId = $filter->supplier_id ?? null;
        $status = Constant::ACCOUNT_PAYABLE_STATUSES;

        if(!empty($filter->status)) {
            $status = [$filter->status];
        }

        $baseQuery = AccountPayableService::getBaseQueryIndex();

        if(!empty($supplierId)) {
            $baseQuery->where('goods_receipts.supplier_id', $supplierId);
        }

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

        return $accountPayables->filter(function ($item) use ($status) {
            return in_array($item->status, $status);
        });
    }

    public static function getDetailData($id, $filter) {
        $startDate = $filter->start_date ?? Carbon::now()->subDays(90)->format('d-m-Y');
        $finalDate = $filter->final_date ?? Carbon::now()->format('d-m-Y');
        $status = Constant::ACCOUNT_PAYABLE_STATUSES;

        if(!empty($filter->status)) {
            $status = [$filter->status];
        }

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

        return $accountPayables;
    }

    public static function getExportIndexData($filter) {
        $startDate = $filter->start_date;
        $finalDate = $filter->final_date;
        $supplierId = $filter->supplier_id ?? null;

        $accountPayableStatuses = Constant::ACCOUNT_PAYABLE_STATUSES;
        $status = $accountPayableStatuses;

        if(!empty($filter->status)) {
            $status = [$filter->status];
        }

        $baseQuery = static::getBaseQueryIndex();

        if(!empty($supplierId)) {
            $baseQuery->where('goods_receipts.supplier_id', $supplierId);
        }

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

        return $accountPayables->filter(function ($item) use ($status) {
            return in_array($item->status, $status);
        });
    }

    public static function createData($goodsReceipt) {
        $data = AccountPayable::create([
            'goods_receipt_id' => $goodsReceipt->id,
            'status' => Constant::ACCOUNT_PAYABLE_STATUS_UNPAID,
        ]);

        if($goodsReceipt->payment_amount > 0) {
            static::createPaymentData($data, $goodsReceipt);

            if($goodsReceipt->payment_amount >= $goodsReceipt->grand_total) {
                $data->status = Constant::ACCOUNT_PAYABLE_STATUS_PAID;
            } else {
                $data->status = Constant::ACCOUNT_PAYABLE_STATUS_ONGOING;
            }

            $data->save();
        }

        return true;
    }

    public static function createPaymentData($accountPayable, $goodsReceipt): bool {
        $accountPayable->payments()->create([
            'date' => $goodsReceipt->date,
            'amount' => $goodsReceipt->payment_amount,
        ]);

        return true;
    }

    public static function getAccountPayableByGoodsReceiptId($goodsReceiptId) {
        return AccountPayable::query()->where('goods_receipt_id', $goodsReceiptId)->first();
    }
}
