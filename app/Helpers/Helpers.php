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

function getWarehouseTypeLabel($type): string
{
    return Constant::WAREHOUSE_TYPE_LABELS[$type];
}

function getPurchaseOrderStatusLabel($status): string
{
    return Constant::PURCHASE_ORDER_STATUS_LABELS[$status];
}

function isActiveData($item): string
{
    return empty($item->deleted_at) ? 'Active' : 'Inactive';
}

function formatDate($date, $format) {
    return \Carbon\Carbon::parse($date)->format($format);
}

function formatCurrency($amount) {
    return number_format($amount, 0, '', ',');
}
