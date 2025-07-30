<?php

namespace App\Utilities\Services;

use App\Models\GoodsReceipt;

class GoodsReceiptService
{
    public static function getBaseQueryIndex() {
        return GoodsReceipt::query()
            ->select(
                'goods_receipts.*',
                'warehouses.name AS warehouse_name',
                'suppliers.name AS supplier_name',
                'users.username AS user_name'
            )
            ->leftJoin('warehouses', 'warehouses.id', 'goods_receipts.warehouse_id')
            ->leftJoin('suppliers', 'suppliers.id', 'goods_receipts.supplier_id')
            ->leftJoin('users', 'users.id', 'goods_receipts.user_id');
    }
}
