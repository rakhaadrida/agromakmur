<?php

namespace App\Utilities\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesRecapService
{
    public static function getBaseQueryProductIndex($startDate, $finalDate) {
        $branchIds = UserService::findBranchIdsByUserId(Auth::id());

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
                        DB::raw('COUNT(DISTINCT(sales_orders.id)) AS invoice_count'),
                        DB::raw('SUM(sales_order_items.actual_quantity) AS total_quantity'),
                        DB::raw('SUM(sales_order_items.total) AS grand_total'),
                    )
                    ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_items.sales_order_id')
                    ->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
                    ->where('sales_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
                    ->where('sales_orders.status', '!=', 'CANCELLED')
                    ->when(!isUserSuperAdmin(), function ($q) use ($branchIds) {
                        $q->whereIn('sales_orders.branch_id', $branchIds);
                    })
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

    public static function getBaseQueryProductDetail($id, $startDate, $finalDate, $customerId) {
        $baseQuery = SalesOrder::query()
            ->select(
                'sales_orders.id AS order_id',
                'sales_orders.date AS order_date',
                'sales_orders.number AS order_number',
                'customers.id AS customer_id',
                'customers.name AS customer_name',
                'sales_order_items.product_name AS product_name',
                'sales_order_items.unit_name AS unit_name',
                'sales_order_items.quantity AS quantity',
                'sales_order_items.price AS price',
                'sales_order_items.total AS total',
            )
            ->joinSub(
                DB::table('sales_order_items')
                    ->select(
                        'sales_order_items.sales_order_id',
                        DB::raw('MAX(products.name) AS product_name'),
                        DB::raw('SUM(sales_order_items.actual_quantity) AS quantity'),
                        DB::raw('MAX(sales_order_items.price) AS price'),
                        DB::raw('SUM(sales_order_items.total) AS total'),
                        DB::raw('MAX(units.name) AS unit_name')
                    )
                    ->join('products', 'products.id', '=', 'sales_order_items.product_id')
                    ->join('units', 'units.id', '=', 'products.unit_id')
                    ->where(function ($query) use ($id) {
                        if($id) {
                            $query->where('products.id', $id);
                        }
                    })
                    ->whereNull('sales_order_items.deleted_at')
                    ->groupBy('sales_order_items.sales_order_id')
                    ->groupBy('sales_order_items.product_id'),
                'sales_order_items',
                'sales_orders.id',
                'sales_order_items.sales_order_id'
            )
            ->join('customers', 'customers.id', '=', 'sales_orders.customer_id')
            ->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('sales_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->where('sales_orders.status', '!=', 'CANCELLED')
            ->whereNull('sales_orders.deleted_at');

        if($customerId) {
            $baseQuery->where('customers.id', $customerId);
        }

        if(!$id) {
            $baseQuery = $baseQuery->orderBy('sales_order_items.product_name');
        }

        return $baseQuery
            ->orderByDesc('sales_orders.date')
            ->orderByDesc('sales_orders.id')
            ->get();
    }

    public static function getBaseQueryCustomerIndex($startDate, $finalDate) {
        $branchIds = UserService::findBranchIdsByUserId(Auth::id());

        return Customer::query()
            ->select(
                'customers.id AS id',
                'customers.name AS customer_name',
                'sales_orders.invoice_count AS invoice_count',
                'sales_orders.subtotal AS subtotal',
                'sales_orders.tax_amount AS tax_amount',
                'sales_orders.grand_total AS grand_total',
            )
            ->joinSub(
                DB::table('sales_orders')
                    ->select(
                        'sales_orders.customer_id',
                        DB::raw('COUNT(DISTINCT(sales_orders.id)) AS invoice_count'),
                        DB::raw('SUM(sales_orders.subtotal) AS subtotal'),
                        DB::raw('SUM(sales_orders.tax_amount) AS tax_amount'),
                        DB::raw('SUM(sales_orders.grand_total) AS grand_total'),
                    )
                    ->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
                    ->where('sales_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
                    ->where('sales_orders.status', '!=', 'CANCELLED')
                    ->when(!isUserSuperAdmin(), function ($q) use ($branchIds) {
                        $q->whereIn('sales_orders.branch_id', $branchIds);
                    })
                    ->whereNull('sales_orders.deleted_at')
                    ->groupBy('sales_orders.customer_id'),
                'sales_orders',
                'customers.id',
                'sales_orders.customer_id'
            )
            ->orderBy('customers.name')
            ->get();
    }

    public static function getBaseQueryCustomerDetail($id, $startDate, $finalDate, $productId) {
        $baseQuery = SalesOrder::query()
            ->select(
                'sales_orders.id AS order_id',
                'sales_orders.date AS order_date',
                'sales_orders.number AS order_number',
                'customers.name AS customer_name',
                'products.id AS product_id',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'sales_order_items.unit_name AS unit_name',
                'sales_order_items.quantity AS quantity',
                'sales_order_items.price AS price',
                'sales_order_items.total AS total',
            )
            ->joinSub(
                DB::table('sales_order_items')
                    ->select(
                        'sales_order_items.sales_order_id',
                        'sales_order_items.product_id',
                        DB::raw('SUM(sales_order_items.actual_quantity) AS quantity'),
                        DB::raw('MAX(sales_order_items.price) AS price'),
                        DB::raw('SUM(sales_order_items.total) AS total'),
                        DB::raw('MAX(units.name) AS unit_name')
                    )
                    ->join('products', 'products.id', '=', 'sales_order_items.product_id')
                    ->join('units', 'units.id', '=', 'products.unit_id')
                    ->whereNull('sales_order_items.deleted_at')
                    ->groupBy('sales_order_items.sales_order_id')
                    ->groupBy('sales_order_items.product_id'),
                'sales_order_items',
                'sales_orders.id',
                'sales_order_items.sales_order_id'
            )
            ->join('customers', 'customers.id', '=', 'sales_orders.customer_id')
            ->join('products', 'products.id', '=', 'sales_order_items.product_id')
            ->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('sales_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->where('sales_orders.status', '!=', 'CANCELLED')
            ->whereNull('sales_orders.deleted_at');

        if($id) {
            $baseQuery->where('customers.id', $id);
        }

        if($productId) {
            $baseQuery->where('products.id', $productId);
        }

        if(!$id) {
            $baseQuery = $baseQuery->orderBy('customers.name');
        }

        return $baseQuery
            ->orderByDesc('sales_orders.date')
            ->orderByDesc('sales_orders.id')
            ->get();
    }
}
