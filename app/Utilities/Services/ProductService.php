<?php

namespace App\Utilities\Services;

use App\Models\GoodsReceipt;
use App\Models\ProductConversion;
use App\Models\ProductPrice;
use App\Models\ProductStock;
use App\Models\ProductStockLog;
use App\Models\ProductTransfer;
use App\Models\SalesOrder;
use App\Models\SalesReturn;
use App\Utilities\Constant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public static function getProductStockQuery($productId, $warehouseId) {
        return ProductStock::query()
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->whereNull('deleted_at')
            ->first();
    }

    public static function getTotalProductStock() {
        return ProductStock::query()
            ->select(
                'product_stocks.product_id',
                DB::raw('SUM(product_stocks.stock) as total_stock')
            )
            ->whereNull('deleted_at')
            ->groupBy('product_stocks.product_id')
            ->get();
    }

    public static function updateProductStockIncrement($productId, $productStock, $actualQuantity, $transactionId, $warehouseId, $supplierId = null, $finalAmount = null, $isReturn = false) {
        $initialStock = $productStock ? $productStock->stock : 0;

        if($productStock) {
            $productStock->increment('stock', $actualQuantity);
        } else {
            ProductStock::create([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'stock' => $actualQuantity
            ]);
        }

        if(!$isReturn) {
            static::createProductStockLog($transactionId, $productId, $warehouseId, $initialStock, $actualQuantity, $supplierId, $finalAmount);
        }

        return true;
    }

    public static function createProductStockLog($transactionId, $productId, $warehouseId, $initialStock, $actualQuantity, $supplierId = null, $finalAmount = null, $customerId = null) {
        $subjectType = ProductTransfer::class;
        $type = Constant::PRODUCT_STOCK_LOG_TYPE_PRODUCT_TRANSFER;

        if($supplierId) {
            $subjectType = GoodsReceipt::class;
            $type = Constant::PRODUCT_STOCK_LOG_TYPE_GOODS_RECEIPT;
        } else if($customerId) {
            if($finalAmount) {
                $subjectType = SalesOrder::class;
                $type = Constant::PRODUCT_STOCK_LOG_TYPE_SALES_ORDER;
            } else {
                $subjectType = SalesReturn::class;
                $type = Constant::PRODUCT_STOCK_LOG_TYPE_SALES_RETURN;
            }
        }

        ProductStockLog::create([
            'subject_type' => $subjectType,
            'subject_id' => $transactionId,
            'type' => $type,
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'customer_id' => $customerId,
            'supplier_id' => $supplierId,
            'initial_stock' => $initialStock,
            'quantity' => $actualQuantity,
            'final_amount' => $finalAmount,
            'user_id' => Auth::user()->id
        ]);

        return true;
    }

    public static function deleteProductStockLog($transactionId, $productId, $warehouseId, $type) {
        $stockLogs = ProductStockLog::query()
            ->where('subject_id', $transactionId)
            ->where('type', $type)
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->whereNull('deleted_at')
            ->get();

        if($stockLogs) {
            foreach ($stockLogs as $stockLog) {
                $stockLog->delete();
            }
        }

        return true;
    }

    public static function findProductConversions($productIds) {
        return ProductConversion::query()
            ->whereIn('product_id', $productIds)
            ->whereNull('deleted_at')
            ->get();
    }

    public static function findProductPrices($productIds) {
        return ProductPrice::query()
            ->whereIn('product_id', $productIds)
            ->whereNull('deleted_at')
            ->get();
    }
}
