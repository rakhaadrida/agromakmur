<?php

namespace App\Utilities\Services;

use App\Models\Product;
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
}
