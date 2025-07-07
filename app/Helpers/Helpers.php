<?php

use App\Utilities\Constant;
use Illuminate\Support\Facades\Auth;

function isUserSuperAdmin(): string
{
    $userRole = Auth::user()->role ?? null;

    return in_array($userRole, [Constant::USER_ROLE_SUPER_ADMIN]);
}

function isUserAdmin(): string
{
    $userRole = Auth::user()->role ?? null;

    return in_array($userRole, [Constant::USER_ROLE_SUPER_ADMIN, Constant::USER_ROLE_ADMIN]);
}

function isUserAdminOnly(): string
{
    $userRole = Auth::user()->role ?? null;

    return in_array($userRole, [Constant::USER_ROLE_ADMIN]);
}

function isUserFinance(): string
{
    $userRole = Auth::user()->role ?? null;

    return in_array($userRole, [Constant::USER_ROLE_FINANCE]);
}

function isUserWarehouse(): string
{
    $userRole = Auth::user()->role ?? null;

    return in_array($userRole, [Constant::USER_ROLE_WAREHOUSE]);
}

function getUserRoleLabel($role): string
{
    return Constant::USER_ROLE_LABELS[$role];
}
