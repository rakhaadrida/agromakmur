<?php

namespace App\Utilities\Services;

use App\Models\AccountPayable;
use App\Utilities\Constant;

class AccountPayableService
{
    public static function createData($goodsReceipt) {
        AccountPayable::create([
            'goods_receipt_id' => $goodsReceipt->id,
            'status' => Constant::ACCOUNT_PAYABLE_STATUS_UNPAID,
        ]);

        return true;
    }
}
