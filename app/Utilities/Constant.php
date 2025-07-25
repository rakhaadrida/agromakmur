<?php

namespace App\Utilities;

class Constant
{
    const USER_ROLE_SUPER_ADMIN = 'SUPER_ADMIN';
    const USER_ROLE_ADMIN = 'ADMIN';
    const USER_ROLE_FINANCE = 'FINANCE';
    const USER_ROLE_WAREHOUSE = 'WAREHOUSE';
    const USER_ROLES = [
        self::USER_ROLE_SUPER_ADMIN,
        self::USER_ROLE_ADMIN,
        self::USER_ROLE_FINANCE,
        self::USER_ROLE_WAREHOUSE,
    ];
    const USER_ROLE_LABELS = [
        self::USER_ROLE_SUPER_ADMIN => 'Super Admin',
        self::USER_ROLE_ADMIN => 'Admin',
        self::USER_ROLE_FINANCE => 'Finance',
        self::USER_ROLE_WAREHOUSE => 'Warehouse',
    ];

    const USER_STATUS_PENDING = 'PENDING';
    const USER_STATUS_ACTIVE = 'ACTIVE';
    const USER_STATUS_LABELS = [
        self::USER_STATUS_PENDING => 'Pending',
        self::USER_STATUS_ACTIVE => 'Active'
    ];

    const WAREHOUSE_TYPE_PRIMARY = 'PRIMARY';
    const WAREHOUSE_TYPE_SECONDARY = 'SECONDARY';
    const WAREHOUSE_TYPE_RETURN = 'RETURN';

    const WAREHOUSE_TYPES = [
        self::WAREHOUSE_TYPE_PRIMARY,
        self::WAREHOUSE_TYPE_SECONDARY,
        self::WAREHOUSE_TYPE_RETURN,
    ];
    const WAREHOUSE_TYPE_LABELS = [
        self::WAREHOUSE_TYPE_PRIMARY => 'Primary',
        self::WAREHOUSE_TYPE_SECONDARY => 'Secondary',
        self::WAREHOUSE_TYPE_RETURN => 'Return'
    ];

    const PURCHASE_ORDER_STATUS_ACTIVE = 'ACTIVE';
    const PURCHASE_ORDER_STATUS_WAITING_APPROVAL = 'WAITING_APPROVAL';
    const PURCHASE_ORDER_STATUS_UPDATED = 'UPDATED';
    const PURCHASE_ORDER_STATUS_CANCELLED = 'CANCELLED';
    const PURCHASE_ORDER_STATUS_LABELS = [
        self::PURCHASE_ORDER_STATUS_ACTIVE => 'Active',
        self::PURCHASE_ORDER_STATUS_WAITING_APPROVAL => 'Waiting Approval',
        self::PURCHASE_ORDER_STATUS_UPDATED => 'Updated',
        self::PURCHASE_ORDER_STATUS_CANCELLED => 'Cancelled',
    ];

    const ACCOUNT_PAYABLE_STATUS_UNPAID = 'UNPAID';
    const ACCOUNT_PAYABLE_STATUS_ONGOING = 'ONGOING';
    const ACCOUNT_PAYABLE_STATUS_PAID = 'PAID';
    const ACCOUNT_PAYABLE_STATUS_LABELS = [
        self::ACCOUNT_PAYABLE_STATUS_UNPAID => 'Unpaid',
        self::ACCOUNT_PAYABLE_STATUS_ONGOING => 'Ongoing',
        self::ACCOUNT_PAYABLE_STATUS_PAID => 'Paid',
    ];
}
