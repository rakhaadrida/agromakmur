<?php

namespace App\Utilities\Services;

use App\Models\BranchWarehouse;
use App\Models\UserBranch;

class BranchService
{
    public static function createUserBranchByBranch($branch, $userIds, $isUpdate = false) {
        if($isUpdate) {
            static::updateUserBranchByBranch($branch);
        }

        foreach($userIds as $userId) {
            $branch->userBranches()->create([
                'user_id' => $userId
            ]);
        }

        return true;
    }

    public static function createBranchWarehouseByBranch($branch, $warehouseIds, $isUpdate = false) {
        if($isUpdate) {
            static::updateBranchWarehouseByBranch($branch);
        }

        foreach($warehouseIds as $warehouseId) {
            $branch->branchWarehouses()->create([
                'warehouse_id' => $warehouseId
            ]);
        }

        return true;
    }

    public static function updateUserBranchByBranch($branch) {
        $branch->userBranches()->update([
            'is_updated' => 1
        ]);

        $branch->userBranches()->delete();

        return true;
    }

    public static function updateBranchWarehouseByBranch($branch) {
        $branch->branchWarehouses()->update([
            'is_updated' => 1
        ]);

        $branch->branchWarehouses()->delete();

        return true;
    }

    public static function restoreUserBranchByBranchId($branchId) {
        $userBranches = UserBranch::onlyTrashed()
            ->where('is_updated', 0)
            ->whereHas('branch', function($query) {
                $query->where('is_destroy', 0);
            });

        if($branchId) {
            $userBranches->where('branch_id', $branchId);
        }

        $userBranches->restore();

        return true;
    }

    public static function restoreBranchWarehouseByBranchId($branchId) {
        $branchWarehouses = BranchWarehouse::onlyTrashed()
            ->where('is_updated', 0)
            ->whereHas('branch', function($query) {
                $query->where('is_destroy', 0);
            });

        if($branchId) {
            $branchWarehouses->where('branch_id', $branchId);
        }

        $branchWarehouses->restore();

        return true;
    }

    public static function findUserIdsByBranchId($branchId) {
        return UserBranch::query()
            ->where('branch_id', $branchId)
            ->pluck('user_id')
            ->toArray();
    }

    public static function findWarehouseIdsByBranchIds($branchIds) {
        return BranchWarehouse::query()
            ->whereIn('branch_id', $branchIds)
            ->pluck('warehouse_id')
            ->toArray();
    }
}
