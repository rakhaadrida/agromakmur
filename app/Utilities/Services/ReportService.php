<?php

namespace App\Utilities\Services;

use App\Models\GoodsReceiptItem;
use App\Models\SalesOrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public static function getIncomingItemsData($startDate, $finalDate) {
        return GoodsReceiptItem::query()
            ->select(
                'suppliers.id AS supplier_id',
                'suppliers.name AS supplier_name',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'units.name AS unit_name',
                'warehouses.name AS warehouse_name',
                DB::raw('SUM(goods_receipt_items.actual_quantity) AS total_quantity')
            )
            ->join('products', 'products.id', '=', 'goods_receipt_items.product_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->join('goods_receipts', 'goods_receipts.id', '=', 'goods_receipt_items.goods_receipt_id')
            ->join('suppliers', 'suppliers.id', '=', 'goods_receipts.supplier_id')
            ->join('warehouses', 'warehouses.id', '=', 'goods_receipts.warehouse_id')
            ->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('goods_receipts.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->where('goods_receipts.status', '!=', 'CANCELLED')
            ->groupBy('suppliers.id', 'products.id', 'warehouses.id')
            ->orderBy('suppliers.name')
            ->orderBy('products.name')
            ->orderBy('warehouses.name')
            ->get();
    }

    public static function getOutgoingItemsData($startDate, $finalDate) {
        return SalesOrderItem::query()
            ->select(
                'customers.id AS customer_id',
                'customers.name AS customer_name',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'warehouses.name AS warehouse_name',
                'units.name AS unit_name',
                DB::raw('SUM(sales_order_items.actual_quantity) AS total_quantity')
            )
            ->join('warehouses', 'warehouses.id', '=', 'sales_order_items.warehouse_id')
            ->join('products', 'products.id', '=', 'sales_order_items.product_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_items.sales_order_id')
            ->join('customers', 'customers.id', '=', 'sales_orders.customer_id')
            ->where('sales_orders.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('sales_orders.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->where('sales_orders.status', '!=', 'CANCELLED')
            ->groupBy('customers.id', 'products.id', 'warehouses.id')
            ->orderBy('customers.name')
            ->orderBy('products.name')
            ->orderBy('warehouses.name')
            ->get();
    }
}
