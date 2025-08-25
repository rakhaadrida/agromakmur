<?php

namespace App\Utilities\Services;

use App\Models\SalesReturn;
use Illuminate\Support\Facades\DB;

class SalesReturnService
{
    public static function getBaseQueryIndex() {
        return SalesReturn::query()
            ->select(
                'sales_returns.*',
                'sales_orders.number AS sales_order_number',
                'customers.name AS customer_name',
                DB::raw('SUM(sales_return_items.quantity) AS quantity'),
                DB::raw('SUM(sales_return_items.delivered_quantity) AS delivered_quantity'),
                DB::raw('SUM(sales_return_items.cut_bill_quantity) AS cut_bill_quantity'),
            )
            ->join('sales_orders', 'sales_orders.id', 'sales_returns.sales_order_id')
            ->join('customers', 'customers.id', 'sales_returns.customer_id')
            ->leftJoin('sales_return_items', 'sales_return_items.sales_return_id', 'sales_returns.id')
            ->whereNull('sales_return_items.deleted_at')
            ->groupBy('sales_returns.id');
    }
}
