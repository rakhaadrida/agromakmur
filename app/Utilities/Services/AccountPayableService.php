<?php

namespace App\Utilities\Services;

use App\Models\AccountPayable;
use App\Models\Supplier;
use App\Utilities\Constant;
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
                DB::raw('SUM(payments.total_payment) AS payment_amount')
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
                'suppliers.name AS supplier_name',
                DB::raw('SUM(account_payable_payments.amount) AS payment_amount')
            )
            ->join('goods_receipts', 'goods_receipts.id', 'account_payables.goods_receipt_id')
            ->join('suppliers', 'suppliers.id', 'goods_receipts.supplier_id')
            ->leftJoin('account_payable_payments', 'account_payable_payments.account_payable_id', 'account_payables.id')
            ->whereNull('account_payable_payments.deleted_at')
            ->groupBy('account_payables.id');
    }

    public static function createData($goodsReceipt) {
        AccountPayable::create([
            'goods_receipt_id' => $goodsReceipt->id,
            'status' => Constant::ACCOUNT_PAYABLE_STATUS_UNPAID,
        ]);

        return true;
    }
}
