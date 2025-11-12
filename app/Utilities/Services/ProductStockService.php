<?php

namespace App\Utilities\Services;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Warehouse;

class ProductStockService
{
    public static function createStockByProduct($product) {
        $warehouses = Warehouse::all();

        foreach($warehouses as $warehouse) {
            $product->productStocks()->create([
                'warehouse_id' => $warehouse->id,
                'stock' => 0
            ]);
        }

        return true;
    }

    public static function createStockByWarehouse($warehouse) {
        $products = Product::query()
            ->where('is_destroy', 0)
            ->get();

        foreach($products as $product) {
            $warehouse->productStocks()->create([
                'product_id' => $product->id,
                'stock' => 0,
                'deleted_at' => $product->deleted_at
            ]);
        }

        return true;
    }

    public static function restoreStockByWarehouseId($warehouseId) {
        $productStocks = ProductStock::onlyTrashed()->where('is_updated', 0);

        if($warehouseId) {
            $productStocks->where('warehouse_id', $warehouseId);
        }

        $productStocks->restore();

        return true;
    }

    public static function restoreStockByProductId($productId) {
        $productStocks = ProductStock::onlyTrashed()
            ->where('product_id', $productId);

        $productStocks->restore();

        return true;
    }

    public static function findProductStocksByProductIds($productIds) {
        return ProductStock::query()
            ->whereIn('product_id', $productIds)
            ->whereNull('deleted_at')
            ->get();
    }
}
