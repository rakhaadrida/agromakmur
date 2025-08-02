<?php

namespace App\Utilities\Services;

use App\Models\ProductStock;

class ProductService
{
    public static function getProductStockQuery($productId, $warehouseId) {
        return ProductStock::query()
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->whereNull('deleted_at')
            ->first();
    }

    public static function updateProductStockIncrement($productId, $productStock, $actualQuantity, $warehouseId) {
        if($productStock) {
            $productStock->increment('stock', $actualQuantity);
        } else {
            ProductStock::create([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'stock' => $actualQuantity
            ]);
        }

        return true;
    }
}
