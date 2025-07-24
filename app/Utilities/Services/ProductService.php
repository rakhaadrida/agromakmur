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
}
