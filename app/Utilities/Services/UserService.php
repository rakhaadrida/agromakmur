<?php

namespace App\Utilities\Services;

use App\Models\User;
use App\Utilities\Constant;

class UserService
{
    public static function getSuperAdminUsers() {
        return User::query()
            ->where('role', Constant::USER_ROLE_SUPER_ADMIN)
            ->get();
    }
}
