<?php

namespace App\Utilities\Services;

use App\Models\PlanOrder;

class PlanOrderService
{
    public static function getBaseQueryIndex() {
        return PlanOrder::query()
            ->select(
                'plan_orders.*',
                'branches.name AS branch_name',
                'suppliers.name AS supplier_name',
                'suppliers.address AS supplier_address',
                'users.username AS user_name'
            )
            ->leftJoin('branches', 'branches.id', 'plan_orders.branch_id')
            ->leftJoin('suppliers', 'suppliers.id', 'plan_orders.supplier_id')
            ->leftJoin('users', 'users.id', 'plan_orders.user_id');
    }
}
