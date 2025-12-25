<?php

namespace App\Utilities\Services;

use App\Models\AccountReceivable;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public static function getBaseQueryTotalReceivable() {
        return AccountReceivable::query()
            ->select(
                DB::raw('SUM(grand_total) AS grand_total'),
                DB::raw('COUNT(account_receivables.id) AS invoice_count'),
                DB::raw('SUM(payments.total_payment) AS payment_amount'),
                DB::raw('SUM(returns.total_return) AS return_amount')
            )
            ->join('sales_orders', 'sales_orders.id', 'account_receivables.sales_order_id')
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
            ->leftJoinSub(
                DB::table('account_receivable_returns')
                    ->select(
                        'account_receivable_returns.account_receivable_id',
                        DB::raw('SUM(account_receivable_returns.total) AS total_return')
                    )
                    ->whereNull('account_receivable_returns.deleted_at')
                    ->groupBy('account_receivable_returns.account_receivable_id'),
                'returns',
                'returns.account_receivable_id',
                'account_receivables.id'
            );
    }
}
