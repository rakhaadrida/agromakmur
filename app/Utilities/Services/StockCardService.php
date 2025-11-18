<?php

namespace App\Utilities\Services;

use App\Models\ProductStockLog;
use App\Utilities\Constant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StockCardService
{
    public static function getBaseQueryProductIndex($startDate, $finalDate, $productId) {
        $baseQuery = ProductStockLog::query()
            ->select(
                'product_stock_logs.*',
                'customers.name AS customer_name',
                'suppliers.name AS supplier_name',
                'warehouses.name AS warehouse_name',
            )
            ->leftJoin('customers', 'customers.id', '=', 'product_stock_logs.customer_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'product_stock_logs.supplier_id')
            ->leftJoin('warehouses', 'warehouses.id', '=', 'product_stock_logs.warehouse_id')
            ->where('product_stock_logs.subject_date', '>=',  Carbon::parse($startDate)->startOfDay())
            ->where('product_stock_logs.subject_date', '<=',  Carbon::parse($finalDate)->endOfDay());

        if($productId) {
            $baseQuery->where('product_stock_logs.product_id', $productId);
        }

        if(!isUserSuperAdmin()) {
            $branchIds = UserService::findBranchIdsByUserId(Auth::id());
            $warehouseIds = BranchService::findWarehouseIdsByBranchIds($branchIds);

            $baseQuery->where(function ($query) use ($branchIds, $warehouseIds) {
                $query->whereIn('product_stock_logs.branch_id', $branchIds)
                    ->orWhere(function ($subQuery) use ($warehouseIds) {
                        $subQuery->where('product_stock_logs.type', Constant::PRODUCT_STOCK_LOG_TYPE_PRODUCT_TRANSFER)
                            ->whereIn('product_stock_logs.warehouse_id', $warehouseIds);
                    });
            });

        }

        return $baseQuery
            ->whereNull('product_stock_logs.deleted_at')
            ->orderBy('product_stock_logs.subject_date')
            ->orderBy('product_stock_logs.type')
            ->get();
    }
}
