<?php

namespace App\Utilities\Services;

use App\Models\ProductTransfer;

class ProductTransferService
{
    public static function getBaseQueryIndex() {
        return ProductTransfer::query()
            ->select(
                'product_transfers.*',
                'users.username AS user_name'
            )
            ->leftJoin('users', 'users.id', 'product_transfers.user_id');
    }
}
