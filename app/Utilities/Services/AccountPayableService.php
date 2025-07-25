<?php

namespace App\Utilities\Services;

use App\Models\AccountPayable;
use App\Utilities\Constant;

class AccountPayableService
{
    public static function createData($purchaseOrder) {
        AccountPayable::create([
            'purchase_order_id' => $purchaseOrder->id,
            'status' => Constant::ACCOUNT_PAYABLE_STATUS_UNPAID,
        ]);

        return true;
    }
}
