<?php

namespace App\Utilities\Services;

use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseRecapService
{
    public static function getBaseQueryProductIndex($startDate, $finalDate) {
        return Product::query()
            ->select(
                'products.id AS id',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'goods_receipt_items.invoice_count AS invoice_count',
                'goods_receipt_items.total_quantity AS total_quantity',
                'goods_receipt_items.total AS grand_total',
                'units.name AS unit_name',
            )
            ->joinSub(
                DB::table('goods_receipt_items')
                    ->select(
                        'goods_receipt_items.product_id',
                        DB::raw('COUNT(DISTINCT(goods_receipts.id)) AS invoice_count'),
                        DB::raw('SUM(goods_receipt_items.actual_quantity) AS total_quantity'),
                        DB::raw('SUM(goods_receipt_items.total) AS total'),
                    )
                    ->join('goods_receipts', 'goods_receipts.id', '=', 'goods_receipt_items.goods_receipt_id')
                    ->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay())
                    ->where('goods_receipts.date', '<=',  Carbon::parse($finalDate)->endOfDay())
                    ->where('goods_receipts.status', '!=', 'CANCELLED')
                    ->whereNull('goods_receipt_items.deleted_at')
                    ->whereNull('goods_receipts.deleted_at')
                    ->groupBy('goods_receipt_items.product_id'),
                'goods_receipt_items',
                'products.id',
                'goods_receipt_items.product_id'
            )
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->orderBy('products.name')
            ->get();
    }

    public static function getBaseQueryProductDetail($id, $startDate, $finalDate, $supplierId) {
        $baseQuery = GoodsReceipt::query()
            ->select(
                'goods_receipts.id AS receipt_id',
                'goods_receipts.date AS receipt_date',
                'goods_receipts.number AS receipt_number',
                'suppliers.id AS supplier_id',
                'suppliers.name AS supplier_name',
                'goods_receipt_items.product_name AS product_name',
                'goods_receipt_items.unit_name AS unit_name',
                'goods_receipt_items.quantity AS quantity',
                'goods_receipt_items.price AS price',
                'goods_receipt_items.wages AS wages',
                'goods_receipt_items.shipping_cost AS shipping_cost',
                'goods_receipt_items.total AS total',
            )
            ->joinSub(
                DB::table('goods_receipt_items')
                    ->select(
                        'goods_receipt_items.goods_receipt_id',
                        DB::raw('MAX(products.name) AS product_name'),
                        DB::raw('SUM(goods_receipt_items.actual_quantity) AS quantity'),
                        DB::raw('MAX(goods_receipt_items.price) AS price'),
                        DB::raw('SUM(goods_receipt_items.wages) AS wages'),
                        DB::raw('SUM(goods_receipt_items.shipping_cost) AS shipping_cost'),
                        DB::raw('SUM(goods_receipt_items.total) AS total'),
                        DB::raw('MAX(units.name) AS unit_name')
                    )
                    ->join('products', 'products.id', '=', 'goods_receipt_items.product_id')
                    ->join('units', 'units.id', '=', 'products.unit_id')
                    ->where(function ($query) use ($id) {
                        if($id) {
                            $query->where('products.id', $id);
                        }
                    })
                    ->whereNull('goods_receipt_items.deleted_at')
                    ->groupBy('goods_receipt_items.goods_receipt_id')
                    ->groupBy('goods_receipt_items.product_id'),
                'goods_receipt_items',
                'goods_receipts.id',
                'goods_receipt_items.goods_receipt_id'
            )
            ->join('suppliers', 'suppliers.id', '=', 'goods_receipts.supplier_id')
            ->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('goods_receipts.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->where('goods_receipts.status', '!=', 'CANCELLED')
            ->whereNull('goods_receipts.deleted_at');

        if($supplierId) {
            $baseQuery->where('suppliers.id', $supplierId);
        }

        if(!$id) {
            $baseQuery = $baseQuery->orderBy('goods_receipt_items.product_name');
        }

        return $baseQuery
            ->orderByDesc('goods_receipts.date')
            ->orderByDesc('goods_receipts.id')
            ->get();
    }

    public static function getBaseQuerySupplierIndex($startDate, $finalDate) {
        return Supplier::query()
            ->select(
                'suppliers.id AS id',
                'suppliers.name AS supplier_name',
                'goods_receipts.invoice_count AS invoice_count',
                'goods_receipts.subtotal AS subtotal',
                'goods_receipts.tax_amount AS tax_amount',
                'goods_receipts.grand_total AS grand_total',
            )
            ->joinSub(
                DB::table('goods_receipts')
                    ->select(
                        'goods_receipts.supplier_id',
                        DB::raw('COUNT(DISTINCT(goods_receipts.id)) AS invoice_count'),
                        DB::raw('SUM(goods_receipts.subtotal) AS subtotal'),
                        DB::raw('SUM(goods_receipts.tax_amount) AS tax_amount'),
                        DB::raw('SUM(goods_receipts.grand_total) AS grand_total'),
                    )
                    ->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay())
                    ->where('goods_receipts.date', '<=',  Carbon::parse($finalDate)->endOfDay())
                    ->where('goods_receipts.status', '!=', 'CANCELLED')
                    ->whereNull('goods_receipts.deleted_at')
                    ->groupBy('goods_receipts.supplier_id'),
                'goods_receipts',
                'suppliers.id',
                'goods_receipts.supplier_id'
            )
            ->orderBy('suppliers.name')
            ->get();
    }

    public static function getBaseQuerySupplierDetail($id, $startDate, $finalDate, $productId) {
        $baseQuery = GoodsReceipt::query()
            ->select(
                'goods_receipts.id AS receipt_id',
                'goods_receipts.date AS receipt_date',
                'goods_receipts.number AS receipt_number',
                'suppliers.name AS supplier_name',
                'products.id AS product_id',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'goods_receipt_items.unit_name AS unit_name',
                'goods_receipt_items.quantity AS quantity',
                'goods_receipt_items.price AS price',
                'goods_receipt_items.wages AS wages',
                'goods_receipt_items.shipping_cost AS shipping_cost',
                'goods_receipt_items.total AS total',
            )
            ->joinSub(
                DB::table('goods_receipt_items')
                    ->select(
                        'goods_receipt_items.goods_receipt_id',
                        'goods_receipt_items.product_id',
                        DB::raw('SUM(goods_receipt_items.actual_quantity) AS quantity'),
                        DB::raw('MAX(goods_receipt_items.price) AS price'),
                        DB::raw('SUM(goods_receipt_items.wages) AS wages'),
                        DB::raw('SUM(goods_receipt_items.shipping_cost) AS shipping_cost'),
                        DB::raw('SUM(goods_receipt_items.total) AS total'),
                        DB::raw('MAX(units.name) AS unit_name')
                    )
                    ->join('products', 'products.id', '=', 'goods_receipt_items.product_id')
                    ->join('units', 'units.id', '=', 'products.unit_id')
                    ->whereNull('goods_receipt_items.deleted_at')
                    ->groupBy('goods_receipt_items.goods_receipt_id')
                    ->groupBy('goods_receipt_items.product_id'),
                'goods_receipt_items',
                'goods_receipts.id',
                'goods_receipt_items.goods_receipt_id'
            )
            ->join('suppliers', 'suppliers.id', '=', 'goods_receipts.supplier_id')
            ->join('products', 'products.id', '=', 'goods_receipt_items.product_id')
            ->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('goods_receipts.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->where('goods_receipts.status', '!=', 'CANCELLED')
            ->whereNull('goods_receipts.deleted_at');

        if($id) {
            $baseQuery->where('suppliers.id', $id);
        }

        if($productId) {
            $baseQuery->where('products.id', $productId);
        }

        if(!$id) {
            $baseQuery = $baseQuery->orderBy('suppliers.name');
        }

        return $baseQuery
            ->orderByDesc('goods_receipts.date')
            ->orderByDesc('goods_receipts.id')
            ->get();
    }
}
