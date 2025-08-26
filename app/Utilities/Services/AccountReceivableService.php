<?php

namespace App\Utilities\Services;

use App\Models\AccountReceivable;
use App\Utilities\Constant;
use Illuminate\Support\Facades\DB;

class AccountReceivableService
{
    public static function getBaseQueryIndex() {
        return AccountReceivable::query()
            ->select(
                'sales_orders.customer_id',
                'customers.name AS customer_name',
                DB::raw('SUM(grand_total) AS grand_total'),
                DB::raw('COUNT(account_receivables.id) AS invoice_count'),
                DB::raw('SUM(payments.total_payment) AS payment_amount')
            )
            ->join('sales_orders', 'sales_orders.id', 'account_receivables.sales_order_id')
            ->join('customers', 'customers.id', 'sales_orders.customer_id')
            ->leftJoinSub(
                DB::table('account_receivable_payments')
                    ->select(
                        'account_receivable_payments.account_receivable_id',
                        DB::raw('SUM(account_receivable_payments.amount) AS total_payment')
                    )
                    ->whereNull('account_receivable_payments.deleted_at')
                    ->groupBy('account_receivable_payments.account_receivable_id'),
                'payments',
                'payments.account_receivable_id',
                'account_receivables.id'
            )
            ->groupBy('sales_orders.customer_id');
    }

    public static function getBaseQueryDetail() {
        return AccountReceivable::query()
            ->select(
                'account_receivables.*',
                'sales_orders.number',
                'sales_orders.date',
                'sales_orders.customer_id',
                'sales_orders.tempo',
                'sales_orders.type',
                'sales_orders.grand_total',
                'customers.name AS customer_name',
                'marketings.name AS marketing_name',
                DB::raw('SUM(account_receivable_payments.amount) AS payment_amount')
            )
            ->join('sales_orders', 'sales_orders.id', 'account_receivables.sales_order_id')
            ->join('customers', 'customers.id', 'sales_orders.customer_id')
            ->join('marketings', 'marketings.id', 'sales_orders.marketing_id')
            ->leftJoin('account_receivable_payments', 'account_receivable_payments.account_receivable_id', 'account_receivables.id')
            ->whereNull('account_receivable_payments.deleted_at')
            ->groupBy('account_receivables.id');
    }

    public static function createData($salesOrder) {
        AccountReceivable::create([
            'sales_order_id' => $salesOrder->id,
            'status' => Constant::ACCOUNT_RECEIVABLE_STATUS_UNPAID,
        ]);

        return true;
    }

    public static function getAccountReceivableBySalesOrderId($salesOrderId) {
        return AccountReceivable::query()->where('sales_order_id', $salesOrderId)->first();
    }
}
