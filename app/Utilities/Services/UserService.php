<?php

namespace App\Utilities\Services;

use App\Models\User;
use App\Models\UserBranch;
use App\Utilities\Constant;
use Illuminate\Support\Facades\DB;

class UserService
{
    public static function getSuperAdminUsers() {
        return User::query()
            ->where('role', Constant::USER_ROLE_SUPER_ADMIN)
            ->get();
    }

    public static function getAdminUsers() {
        return User::query()
            ->where('role', Constant::USER_ROLE_ADMIN)
            ->get();
    }

    public static function getBaseQueryIndex() {
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
            ->leftJoin('user_branches', 'user_branches.user_id', 'users.id')
            ->leftJoin('branches', 'branches.id', 'user_branches.branch_id')
            ->whereNull('user_branches.deleted_at')
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
