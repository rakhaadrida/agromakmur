<?php

use App\Utilities\Constant;

function isUserAdmin(): string
{
    $userRole = \Illuminate\Support\Facades\Auth::user()->role ?? null;

    return in_array($userRole, [Constant::USER_ROLE_SUPER_ADMIN, Constant::USER_ROLE_ADMIN]);
}
