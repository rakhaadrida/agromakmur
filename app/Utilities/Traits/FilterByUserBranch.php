<?php

namespace App\Utilities\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FilterByUserBranch
{
    protected static function bootFilterByUserBranch(): void
    {
        static::addGlobalScope('user_branch', function (Builder $builder) {
            $user = User::query()->find(Auth::id() ?? 0);

            if (!$user) return;

            $branchIds = $user->userBranches?->pluck('branch_id') ?? collect([]);

            if (!isUserSuperAdmin() || $branchIds->isNotEmpty()) {
                $builder->whereIn($builder->getModel()->getTable() . '.branch_id', $branchIds);
            }
        });
    }
}
