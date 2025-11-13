<?php

namespace App\Utilities\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FilterDeliveryItemByDeliveryOrderBranch
{
    protected static function bootFilterDeliveryItemByDeliveryOrderBranch(): void
    {
        static::addGlobalScope('delivery_item_by_order_branch', function (Builder $builder) {
            $user = User::query()->findOrFail(Auth::id());

            if (!$user) return;

            $branchIds = $user->userBranches?->pluck('branch_id') ?? collect([]);

            if (!isUserSuperAdmin() || $branchIds->isNotEmpty()) {
                $builder->whereHas('deliveryOrder', function ($q) use ($branchIds) {
                    $q->whereIn('branch_id', $branchIds);
                });
            }
        });
    }
}
