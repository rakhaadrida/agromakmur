<?php

namespace App\Utilities\Services;

use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesRecapService
{
    public static function getBaseQueryProductIndex($startDate, $finalDate) {
        return Product::query()
            ->select(
                'products.id AS id',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'sales_order_items.invoice_count AS invoice_count',
                'sales_order_items.total_quantity AS total_quantity',
                'sales_order_items.grand_total AS grand_total',
                'units.name AS unit_name',
            )
            ->joinSub(
                DB::table('sales_order_items')
                    ->select(
                        'sales_order_items.product_id',
                        DB::raw('COUNT(sales_orders.id) AS invoice_count'),
                        DB::raw('SUM(sales_order_items.actual_quantity) AS total_quantity'),
                        DB::raw('SUM(sales_order_items.final_amount) AS grand_total'),
                    )
                    ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_items.sales_order_id')
                    ->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
                    ->where('sales_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
                    ->where('sales_orders.status', '!=', 'CANCELLED')
                    ->whereNull('sales_order_items.deleted_at')
                    ->whereNull('sales_orders.deleted_at')
                    ->groupBy('sales_order_items.product_id'),
                'sales_order_items',
                'products.id',
                'sales_order_items.product_id'
            )
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->orderBy('products.name')
            ->get();
    }

    public static function getBaseQueryCustomerIndex($startDate, $finalDate) {
        return Customer::query()
            ->select(
                'customers.id AS id',
                'customers.name AS customer_name',
                'sales_orders.invoice_count AS invoice_count',
                'sales_orders.subtotal AS subtotal',
                'sales_orders.invoice_discount AS invoice_discount',
                'sales_orders.tax_amount AS tax_amount',
                'sales_orders.grand_total AS grand_total',
            )
            ->joinSub(
                DB::table('sales_orders')
                    ->select(
                        'sales_orders.customer_id',
                        DB::raw('COUNT(sales_orders.id) AS invoice_count'),
                        DB::raw('SUM(sales_orders.subtotal) AS subtotal'),
                        DB::raw('SUM(sales_orders.discount_amount) AS invoice_discount'),
                        DB::raw('SUM(sales_orders.tax_amount) AS tax_amount'),
                        DB::raw('SUM(sales_orders.grand_total) AS grand_total'),
                    )
                    ->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
                    ->where('sales_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
                    ->where('sales_orders.status', '!=', 'CANCELLED')
                    ->whereNull('sales_orders.deleted_at')
                    ->groupBy('sales_orders.customer_id'),
                'sales_orders',
                'customers.id',
                'sales_orders.customer_id'
            )
            ->orderBy('customers.name')
            ->get();
    }
}
