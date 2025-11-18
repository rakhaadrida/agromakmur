<?php

namespace App\Utilities\Services;

use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\ProductConversion;
use App\Models\ProductPrice;
use App\Models\ProductStock;
use App\Models\ProductStockLog;
use App\Models\ProductTransfer;
use App\Models\PurchaseReturn;
use App\Models\SalesOrder;
use App\Models\SalesReturn;
use App\Utilities\Constant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public static function getBaseQueryIndex() {
        return Product::query()
            ->select(
                'products.*',
                'categories.name AS category_name',
                'subcategories.name AS subcategory_name',
                'units.name AS unit_name'
            )
            ->leftJoin('categories', 'categories.id', 'products.category_id')
            ->leftJoin('subcategories', 'subcategories.id', 'products.subcategory_id')
            ->leftJoin('units', 'units.id', 'products.unit_id')
            ->get();
    }

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

    public static function createProductConversionByProduct($product, $unitId, $quantity, $isUpdate = false) {
        if($isUpdate) {
            static::updateProductConversionByProduct($product);
        }

        $product->productConversions()->create([
            'unit_id' => $unitId,
            'quantity' => $quantity
        ]);

        return true;
    }

    public static function createProductPriceByProduct($product, $prices, $data, $isUpdate = false) {
        if($isUpdate) {
            static::updateProductPriceByProduct($product);
        }

        foreach ($prices as $index => $price) {
            $product->productPrices()->create([
                'price_id' => $data->get('price_id')[$index],
                'base_price' => $data->get('base_price')[$index],
                'tax_amount' => $data->get('tax_amount')[$index],
                'price' => $price
            ]);
        }

        return true;
    }

    public static function createProductStockByProduct($product, $stocks, $data, $isUpdate = false) {
        if($isUpdate) {
            static::updateProductStockByProduct($product);
        }

        foreach ($stocks as $index => $stock) {
            $product->productStocks()->create([
                'warehouse_id' => $data->get('warehouse_id')[$index],
                'stock' => $stock
            ]);

            /* ProductService::createProductStockLog(
                $product->id,
                Carbon::now(),
                $product->id,
                $request->get('warehouse_id')[$index],
                0,
                $stock,
                null,
                null
            ); */
        }

        return true;
    }

    public static function updateProductConversionByProduct($product) {
        $product->productConversions()->update([
            'is_updated' => 1
        ]);

        $product->productConversions()->delete();

        return true;
    }

    public static function updateProductPriceByProduct($product) {
        $product->productPrices()->update([
            'is_updated' => 1
        ]);

        $product->productPrices()->delete();

        return true;
    }

    public static function updateProductStockByProduct($product) {
        $product->productStocks()->update([
            'is_updated' => 1
        ]);

        $product->productStocks()->delete();

        return true;
    }

    public static function restoreProductPricesByProductId($productId) {
        $prices = ProductPrice::onlyTrashed()
            ->where('is_updated', 0)
            ->whereHas('product', function($query) {
                $query->where('is_destroy', 0);
            });

        if($productId) {
            $prices->where('product_id', $productId);
        }

        $prices->restore();

        return true;
    }

    public static function restoreProductConversionsByProductId($productId) {
        $conversions = ProductConversion::onlyTrashed()
            ->where('is_updated', 0)
            ->whereHas('product', function($query) {
                $query->where('is_destroy', 0);
            });

        if($productId) {
            $conversions->where('product_id', $productId);
        }

        $conversions->restore();

        return true;
    }

    public static function updateProductCategoryBySubcategory($subcategory) {
        $products = Product::query()
            ->where('subcategory_id', $subcategory->id)
            ->get();

        foreach($products as $product) {
            $product->category_id = $subcategory->category_id;
            $product->save();
        }

        return true;
    }

    public static function updateProductStockIncrement($productId, $productStock, $actualQuantity, $transactionId, $transactionDate, $warehouseId, $supplierId = null, $branchId = null, $finalAmount = null, $isReturn = false) {
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
            static::createProductStockLog($transactionId, $transactionDate, $productId, $warehouseId, $initialStock, $actualQuantity, $branchId, $supplierId, $finalAmount);
        }

        return true;
    }

    public static function createProductStockLog($transactionId, $transactionDate, $productId, $warehouseId, $initialStock, $actualQuantity, $branchId = null, $supplierId = null, $finalAmount = null, $customerId = null, $isReturn = false) {
        $subjectType = ProductTransfer::class;
        $type = Constant::PRODUCT_STOCK_LOG_TYPE_PRODUCT_TRANSFER;

        if($supplierId) {
            if(!$isReturn) {
                $subjectType = GoodsReceipt::class;
                $type = Constant::PRODUCT_STOCK_LOG_TYPE_GOODS_RECEIPT;
            } else {
                $subjectType = PurchaseReturn::class;
                $type = Constant::PRODUCT_STOCK_LOG_TYPE_PURCHASE_RETURN;
            }
        } else if($customerId) {
            if(!$isReturn) {
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
            'subject_date' => $transactionDate,
            'type' => $type,
            'branch_id' => $branchId,
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

    public static function findExportProductsByCategoryId($categoryId) {
        return Product::query()
            ->select('products.*', 'subcategories.name AS subcategory_name')
            ->leftJoin('subcategories', 'subcategories.id', '=', 'products.subcategory_id')
            ->where('products.category_id', $categoryId)
            ->whereNull('products.deleted_at')
            ->orderBy('subcategories.id')
            ->orderBy('products.name')
            ->get();
    }
}
