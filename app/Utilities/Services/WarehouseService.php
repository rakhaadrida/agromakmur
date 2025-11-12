<?php

namespace App\Utilities\Services;

use App\Models\BranchWarehouse;
use App\Models\Warehouse;
use App\Utilities\Constant;
use Illuminate\Support\Facades\DB;

class WarehouseService
{
    public static function getReturnWarehouse() {
        return Warehouse::query()
            ->where('type', Constant::WAREHOUSE_TYPE_RETURN)
            ->whereNull('deleted_at')
            ->first();
    }

    public static function getGeneralWarehouse() {
        return Warehouse::query()
            ->where('type', '!=', Constant::WAREHOUSE_TYPE_RETURN)
            ->whereNull('deleted_at')
            ->get();
    }

    public static function getBaseQueryIndex() {
        return Warehouse::query()
            ->select(
                'warehouses.*',
                DB::raw('
                    CASE
                        WHEN COUNT(branches.id) > 2
                            THEN CONCAT(
                                SUBSTRING_INDEX(GROUP_CONCAT(branches.name ORDER BY branches.name SEPARATOR ", "), ", ", 2),
                                ", +", COUNT(branches.id) - 2
                            )
                        ELSE
                            GROUP_CONCAT(branches.name ORDER BY branches.name SEPARATOR ", ")
                    END as branch_name
                '),
            )
            ->leftJoin('branch_warehouses', 'branch_warehouses.warehouse_id', 'warehouses.id')
            ->leftJoin('branches', 'branches.id', 'branch_warehouses.branch_id')
            ->whereNull('branch_warehouses.deleted_at')
            ->groupBy('warehouses.id')
            ->get();
    }

    public static function createBranchWarehouseByWarehouse($warehouse, $branchIds, $isUpdate = false) {
        if($isUpdate) {
            static::updateBranchWarehouseByWarehouse($warehouse);
        }

        foreach($branchIds as $branchId) {
            $warehouse->branchWarehouses()->create([
                'branch_id' => $branchId
            ]);
        }

        return true;
    }

    public static function updateBranchWarehouseByWarehouse($warehouse) {
        $warehouse->branchWarehouses()->update([
            'is_updated' => 1
        ]);

        $warehouse->branchWarehouses()->delete();

        return true;
    }

    public static function restoreBranchWarehouseByWarehouseId($warehouseId) {
        $branchWarehouses = BranchWarehouse::onlyTrashed()
            ->where('is_updated', 0)
            ->whereHas('warehouse', function($query) {
                $query->where('is_destroy', 0);
            });

        if($warehouseId) {
            $branchWarehouses->where('warehouse_id', $warehouseId);
        }

        $branchWarehouses->restore();

        return true;
    }

    public static function findBranchIdsByWarehouseId($warehouseId) {
        return BranchWarehouse::query()
            ->where('warehouse_id', $warehouseId)
            ->pluck('branch_id')
            ->toArray();
    }
}
