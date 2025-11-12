<?php

namespace App\Utilities\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FilterByUserBranchTable
{
    protected static function bootFilterByUserBranchTable(): void
    {
        static::addGlobalScope('user_branch_relation', function (Builder $builder) {
            $user = User::query()->findOrFail(Auth::id());

            if (!$user) return;

            $branchIds = $user->userBranches?->pluck('branch_id') ?? collect([]);

            if (!isUserSuperAdmin() || $branchIds->isNotEmpty()) {
                $builder->whereHas('userBranches', function ($q) use ($branchIds) {
                    $q->whereIn('user_branches.branch_id', $branchIds);
                });
            }
        });
    }
}
