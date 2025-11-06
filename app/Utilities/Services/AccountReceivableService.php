<?php

namespace App\Utilities\Services;

use App\Models\AccountReceivable;
use App\Utilities\Constant;
use Carbon\Carbon;
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
                DB::raw('SUM(payments.total_payment) AS payment_amount'),
                DB::raw('SUM(returns.total_return) AS return_amount')
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
            ->leftJoinSub(
                DB::table('account_receivable_returns')
                    ->select(
                        'account_receivable_returns.account_receivable_id',
                        DB::raw('SUM(account_receivable_returns.final_amount) AS total_return')
                    )
                    ->whereNull('account_receivable_returns.deleted_at')
                    ->groupBy('account_receivable_returns.account_receivable_id'),
                'returns',
                'returns.account_receivable_id',
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
                DB::raw('SUM(payments.total_payment) AS payment_amount'),
                DB::raw('SUM(returns.total_return) AS return_amount'),
                DB::raw('SUM(returns.total_quantity) AS total_quantity'),
            )
            ->join('sales_orders', 'sales_orders.id', 'account_receivables.sales_order_id')
            ->join('customers', 'customers.id', 'sales_orders.customer_id')
            ->join('marketings', 'marketings.id', 'sales_orders.marketing_id')
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
                        DB::raw('SUM(account_receivable_returns.final_amount) AS total_return'),
                        DB::raw('SUM(account_receivable_returns.quantity) AS total_quantity')
                    )
                    ->whereNull('account_receivable_returns.deleted_at')
                    ->groupBy('account_receivable_returns.account_receivable_id'),
                'returns',
                'returns.account_receivable_id',
                'account_receivables.id'
            )
            ->groupBy('account_receivables.id');
    }

    public static function getIndexData($filter) {
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
            } else if($paymentAmount > 0 || $returnAmount > 0) {
                $receivableStatus = Constant::ACCOUNT_RECEIVABLE_STATUS_ONGOING;
            }

            $accountReceivable->outstanding_amount = $outstandingAmount;
            $accountReceivable->status = $receivableStatus;
        }

        return $accountReceivables->filter(function ($item) use ($status) {
            return in_array($item->status, $status);
        });
    }

    public static function getExportIndexData($filter) {
        $startDate = $filter->start_date;
        $finalDate = $filter->final_date;

        $accountReceivableStatuses = Constant::ACCOUNT_RECEIVABLE_STATUSES;
        $status = $accountReceivableStatuses;

        if(!empty($filter->status)) {
            $status = [$filter->status];
        }

        $baseQuery = static::getBaseQueryIndex();

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
            } else if($paymentAmount > 0 || $returnAmount > 0) {
                $receivableStatus = Constant::ACCOUNT_RECEIVABLE_STATUS_ONGOING;
            }

            $accountReceivable->outstanding_amount = $outstandingAmount;
            $accountReceivable->status = $receivableStatus;
        }

        return $accountReceivables->filter(function ($item) use ($status) {
            return in_array($item->status, $status);
        });
    }

    public static function createData($salesOrder) {
        AccountReceivable::create([
            'sales_order_id' => $salesOrder->id,
            'status' => Constant::ACCOUNT_RECEIVABLE_STATUS_UNPAID,
        ]);

        return true;
    }

    public static function deleteData($accountReceivable) {
        $accountReceivable->payments()->delete();
        $accountReceivable->returns()->delete();
        $accountReceivable->delete();

        return true;
    }

    public static function getAccountReceivableBySalesOrderId($salesOrderId) {
        return AccountReceivable::query()->where('sales_order_id', $salesOrderId)->first();
    }
}
