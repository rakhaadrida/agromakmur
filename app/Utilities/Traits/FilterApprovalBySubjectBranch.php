<?php

namespace App\Utilities\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FilterApprovalBySubjectBranch
{
    protected static function bootFilterApprovalBySubjectBranch(): void
    {
        static::addGlobalScope('approval_by_subject_branch', function (Builder $builder) {
            $user = User::query()->findOrFail(Auth::id());

            if (!$user) return;

            $branchIds = $user->userBranches?->pluck('branch_id') ?? collect([]);

            if (!isUserSuperAdmin() || $branchIds->isNotEmpty()) {
                $builder->where(function ($query) use ($branchIds) {
                    $query->whereHas('subject', function ($q) use ($branchIds) {
                        $q->whereIn('branch_id', $branchIds);
                    });
                });
            }
        });
    }
}
