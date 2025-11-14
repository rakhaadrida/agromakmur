<?php

namespace App\Utilities\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FilterItemByGoodsReceiptBranch
{
    protected static function bootFilterItemByGoodsReceiptBranch(): void
    {
        static::addGlobalScope('item_by_receipt_branch', function (Builder $builder) {
            $user = User::query()->findOrFail(Auth::id());

            if (!$user) return;

            $branchIds = $user->userBranches?->pluck('branch_id') ?? collect([]);

            if (!isUserSuperAdmin() || $branchIds->isNotEmpty()) {
                $builder->whereHas('goodsReceipt', function ($q) use ($branchIds) {
                    $q->whereIn('branch_id', $branchIds);
                });
            }
        });
    }
}
