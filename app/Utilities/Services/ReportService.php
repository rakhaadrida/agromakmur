<?php

namespace App\Utilities\Services;

use App\Models\GoodsReceiptItem;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductStock;
use App\Models\SalesOrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public static function getProductHistoryData() {
        $branchIds = UserService::findBranchIdsByUserId(Auth::id());

        return Product::query()
            ->select(
                'products.id AS product_id',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'goods_receipts.id AS latest_id',
                'goods_receipts.date AS latest_date',
                'goods_receipts.number AS latest_number',
                'branches.name AS latest_branch',
                'suppliers.name AS latest_supplier',
                'units.name AS latest_unit',
                'goods_receipt_items.actual_quantity AS latest_quantity',
                'goods_receipt_items.price AS latest_price'
            )
            ->joinSub(
                DB::table('goods_receipt_items')
                    ->select(
                        'goods_receipt_items.product_id',
                        DB::raw('MAX(goods_receipts.date) AS latest_date'),
                        DB::raw('MAX(goods_receipts.id) AS latest_id')
                    )
                    ->join('goods_receipts', 'goods_receipts.id', '=', 'goods_receipt_items.goods_receipt_id')
                    ->where('goods_receipts.status', '!=', 'CANCELLED')
                    ->when(!isUserSuperAdmin(), function ($q) use ($branchIds) {
                        $q->whereIn('goods_receipts.branch_id', $branchIds);
                    })
                    ->whereNull('goods_receipt_items.deleted_at')
                    ->whereNull('goods_receipts.deleted_at')
                    ->groupBy('goods_receipt_items.product_id'),
                'latest_items',
                'products.id',
                'latest_items.product_id'
            )
            ->join('goods_receipt_items', function ($join) {
                $join->on('goods_receipt_items.product_id', '=', 'products.id')
                    ->on('goods_receipt_items.goods_receipt_id', '=', 'latest_items.latest_id')
                    ->whereNull('goods_receipt_items.deleted_at');
            })
            ->join('goods_receipts', 'goods_receipts.id', '=', 'latest_items.latest_id')
            ->join('branches', 'branches.id', '=', 'goods_receipts.branch_id')
            ->join('suppliers', 'suppliers.id', '=', 'goods_receipts.supplier_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->orderBy('products.name')
            ->get();
    }

    public static function getProductHistoryDetail($startDate, $finalDate, $productId, $supplierId = null) {
        $baseQuery = GoodsReceiptItem::query()
            ->select(
                'goods_receipts.id AS receipt_id',
                'goods_receipts.date AS receipt_date',
                'goods_receipts.number AS receipt_number',
                'branches.name AS branch_name',
                'suppliers.id AS supplier_id',
                'suppliers.name AS supplier_name',
                'units.name AS unit_name',
                'goods_receipt_items.actual_quantity AS quantity',
                'goods_receipt_items.price AS price',
                'goods_receipt_items.wages AS wages',
                'goods_receipt_items.shipping_cost AS shipping_cost',
                'goods_receipt_items.cost_price AS cost_price',
                'goods_receipt_items.total AS total',
            )
            ->join('goods_receipts', 'goods_receipts.id', '=', 'goods_receipt_items.goods_receipt_id')
            ->join('branches', 'branches.id', '=', 'goods_receipts.branch_id')
            ->join('suppliers', 'suppliers.id', '=', 'goods_receipts.supplier_id')
            ->join('products', 'products.id', '=', 'goods_receipt_items.product_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->where('products.id', $productId)
            ->where('goods_receipts.date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('goods_receipts.date', '<=',  Carbon::parse($finalDate)->endOfDay())
            ->whereNull('goods_receipt_items.deleted_at')
            ->whereNull('goods_receipts.deleted_at');


        if($supplierId) {
            $baseQuery = $baseQuery->where('goods_receipts.supplier_id', $supplierId);
        }

        if(!isUserSuperAdmin()) {
            $branchIds = UserService::findBranchIdsByUserId(Auth::id());
            $baseQuery = $baseQuery->whereIn('goods_receipts.branch_id', $branchIds);
        }

        return $baseQuery
            ->orderByDesc('goods_receipts.date')
            ->orderByDesc('goods_receipts.id')
            ->get();
    }

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

    public static function getCommonRecapMapProduct($mapProductByCategory): array {
        $products = Product::all();

        foreach($products as $product) {
            $mapProductByCategory[$product->category_id][] = $product;
        }

        return $mapProductByCategory;
    }

    public static function getPriceListMapPrice($mapPriceByProduct): array {
        $productPrices = ProductPrice::query()->whereNull('deleted_at')->get();

        foreach($productPrices as $productPrice) {
            $mapPriceByProduct[$productPrice->product_id][$productPrice->price_id] = $productPrice->price;
        }

        return $mapPriceByProduct;
    }

    public static function getStockRecapMapStock($mapStockByProduct, $mapTotalStockByCategory, $mapTotalStockByCategoryWarehouse): array {
        $productStocks = ProductStock::query()->whereNull('deleted_at')->get();

        foreach($productStocks as $productStock) {
            $mapStockByProduct[$productStock->product_id][$productStock->warehouse_id] = $productStock->stock;
            $mapTotalStockByCategory[$productStock->product->category_id] = ($mapTotalStockByCategory[$productStock->product->category_id] ?? 0) + $productStock->stock;
            $mapTotalStockByCategoryWarehouse[$productStock->product->category_id][$productStock->warehouse_id] = ($mapTotalStockByCategoryWarehouse[$productStock->product->category_id][$productStock->warehouse_id] ?? 0) + $productStock->stock;
        }

        return [$mapStockByProduct, $mapTotalStockByCategory, $mapTotalStockByCategoryWarehouse];
    }

    public static function getValueRecapMapStock($mapStockByProduct, $mapTotalStockByCategory): array {
        $productStocks = ProductService::getTotalProductStock();

        foreach($productStocks as $productStock) {
            $mapStockByProduct[$productStock->product_id] = $productStock->total_stock;
            $mapTotalStockByCategory[$productStock->product->category_id] = ($mapTotalStockByCategory[$productStock->product->category_id] ?? 0) + $productStock->total_stock;
        }

        return [$mapStockByProduct, $mapTotalStockByCategory];
    }

    public static function getValueRecapMapProduct($mapStockByProduct, $mapProductByCategory, $mapTotalValueByCategory): array {
        $products = Product::all();

        foreach($products as $product) {
            $productPrice = $product->mainPrice ? $product->mainPrice->price : 0;
            $totalValue = $productPrice * ($mapStockByProduct[$product->id] ?? 0);

            $product->price = $productPrice;
            $product->total_value = $totalValue;

            $mapProductByCategory[$product->category_id][] = $product;
            $mapTotalValueByCategory[$product->category_id] = ($mapTotalValueByCategory[$product->category_id] ?? 0) + $totalValue;
        }

        return [$mapProductByCategory, $mapTotalValueByCategory];
    }
}
