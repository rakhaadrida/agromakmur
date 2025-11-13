<?php

namespace App\Utilities\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FilterProductTransferByWarehouse
{
    protected static function bootFilterProductTransferByWarehouse(): void
    {
        static::addGlobalScope('transfer_by_warehouse', function (Builder $builder) {
            $user = User::query()->findOrFail(Auth::id());

            if (!$user) return;

            $branchIds = $user->userBranches?->pluck('branch_id') ?? collect([]);

            if (!isUserSuperAdmin() || $branchIds->isNotEmpty()) {
                $builder->whereHas('sourceWarehouse.branches', function ($q) use ($branchIds) {
                    $q->whereIn('branches.id', $branchIds);
                })->orWhereHas('destinationWarehouse.branches', function ($q) use ($branchIds) {
                    $q->whereIn('branches.id', $branchIds);
                });
            }
        });
    }
}
