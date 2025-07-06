<?php

namespace App\Utilities;

class Constant
{
    const USER_ROLE_SUPER_ADMIN = 'SUPER_ADMIN';
    const USER_ROLE_ADMIN = 'ADMIN';
    const USER_ROLE_FINANCE = 'FINANCE';
    const USER_ROLE_WAREHOUSE = 'WAREHOUSE';

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
}
