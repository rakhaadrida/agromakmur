<?php

namespace App\Utilities\Services;

use App\Models\User;
use App\Models\UserBranch;
use App\Utilities\Constant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserService
{
    public static function getSuperAdminUsers($branchId = null) {
        return User::query()
            ->where('role', Constant::USER_ROLE_SUPER_ADMIN)
            ->orWhere(function($query) use ($branchId) {
                $query->where('role', Constant::USER_ROLE_SUPER_ADMIN_BRANCH)
                      ->when($branchId, function($query) use ($branchId) {
                          $query->whereHas('userBranches', function($query) use ($branchId) {
                              $query->where('branch_id', $branchId);
                          });
                      });
            })
            ->get();
    }

    public static function getAdminUsers($branchId) {
        return User::query()
            ->where('role', Constant::USER_ROLE_ADMIN)
            ->when($branchId, function($query) use ($branchId) {
                $query->whereHas('userBranches', function($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                });
            })
            ->get();
    }

    public static function getBaseQueryIndex() {
        $branchIds = UserService::findBranchIdsByUserId(Auth::id());

        return User::query()
            ->select(
                'users.*',
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
            ->leftJoin('user_branches', function ($join) {
                $join->on('user_branches.user_id', 'users.id')
                     ->whereNull('user_branches.deleted_at');
            })
            ->leftJoin('branches', 'branches.id', 'user_branches.branch_id')
            ->when(!isUserSuperAdmin(), function ($q) use ($branchIds) {
                $q->whereIn('user_branches.branch_id', $branchIds);
            })
            ->groupBy('users.id')
            ->get();
    }

    public static function createUserBranchByUser($user, $branchIds, $isUpdate = false) {
        if($isUpdate) {
            static::updateUserBranchByUser($user);
        }

        foreach($branchIds as $branchId) {
            $user->userBranches()->create([
                'branch_id' => $branchId
            ]);
        }

        return true;
    }

    public static function updateUserBranchByUser($user) {
        $user->userBranches()->update([
            'is_updated' => 1
        ]);

        $user->userBranches()->delete();

        return true;
    }

    public static function restoreUserBranchByUserId($userId) {
        $userBranches = UserBranch::onlyTrashed()
            ->where('is_updated', 0)
            ->whereHas('user', function($query) {
                $query->where('is_destroy', 0);
            });

        if($userId) {
            $userBranches->where('user_id', $userId);
        }

        $userBranches->restore();

        return true;
    }

    public static function findBranchIdsByUserId($userId) {
        return UserBranch::query()
            ->where('user_id', $userId)
            ->pluck('branch_id')
            ->toArray();
    }
}
