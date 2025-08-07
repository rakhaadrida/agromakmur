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

function getGoodsReceiptStatusLabel($status): string
{
    return Constant::GOODS_RECEIPT_STATUS_LABELS[$status];
}

function getProductTransferStatusLabel($status): string
{
    return Constant::PRODUCT_TRANSFER_STATUS_LABELS[$status];
}

function getSalesOrderStatusLabel($status): string
{
    return Constant::SALES_ORDER_STATUS_LABELS[$status];
}

function getApprovalTypeLabel($type): string
{
    return Constant::APPROVAL_TYPE_LABELS[$type];
}

function isWaitingApproval($status): bool
{
    return in_array($status, [Constant::PRODUCT_TRANSFER_STATUS_WAITING_APPROVAL, Constant::GOODS_RECEIPT_STATUS_WAITING_APPROVAL]);
}

function isApprovalTypeEdit($type): bool
{
    return $type == Constant::APPROVAL_TYPE_EDIT;
}

function getDueDate($date, $tempo, $format): string
{
    $dueDate = \Carbon\Carbon::parse($date)->add($tempo, 'days');

    return formatDate($dueDate, $format);
}

function getInvoiceAge($date, $tempo): string
{
    $now = \Carbon\Carbon::now();
    $invoiceDate = \Carbon\Carbon::parse($date);

    $diffInDays = $now->diffInDays($invoiceDate);

    return $diffInDays;
}

function getRealQuantity($quantity, $actualQuantity)
{
    return $actualQuantity / $quantity;
}

function isActiveData($item): string
{
    return empty($item->deleted_at) ? 'Active' : 'Inactive';
}

function isSameWarehouse($warehouseId, $itemId): string
{
    return $warehouseId == $itemId;
}

function formatDate($date, $format)
{
    return \Carbon\Carbon::parse($date)->format($format);
}

function formatCurrency($amount)
{
    return number_format($amount, 0, '', ',');
}

function formatPrice($amount)
{
    return number_format($amount, 0, ',', '.');
}

function formatQuantity($amount)
{
    return number_format($amount, 0, ',', '.');
}
