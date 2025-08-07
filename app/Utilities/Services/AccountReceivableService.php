<?php

namespace App\Utilities\Services;

use App\Models\AccountReceivable;
use App\Utilities\Constant;

class AccountReceivableService
{
    public static function createData($salesOrder) {
        AccountReceivable::create([
            'sales_order_id' => $salesOrder->id,
            'status' => Constant::ACCOUNT_RECEIVABLE_STATUS_UNPAID,
        ]);

        return true;
    }
}
