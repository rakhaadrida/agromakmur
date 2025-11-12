<?php

namespace App\Utilities\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FilterStockByBranchWarehouse
{
    protected static function bootFilterStockByBranchWarehouse(): void
    {
        static::addGlobalScope('stock_by_branch', function (Builder $builder) {
            $user = User::query()->findOrFail(Auth::id());

            if (!$user) return;

            $branchIds = $user->userBranches?->pluck('branch_id') ?? collect([]);

            if (!isUserSuperAdmin() || $branchIds->isNotEmpty()) {
                $builder->whereHas('warehouse.branches', function ($q) use ($branchIds) {
                    $q->whereIn('branches.id', $branchIds);
                });
            }
        });
    }
}
