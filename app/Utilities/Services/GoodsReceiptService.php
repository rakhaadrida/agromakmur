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

    public static function mapGoodsReceiptIndex($goodsReceipts) {
        foreach ($goodsReceipts as $goodsReceipt) {
            if(isWaitingApproval($goodsReceipt->status) && isApprovalTypeEdit($goodsReceipt->pendingApproval->type)) {
                static::mapGoodsReceiptApproval($goodsReceipt);
            }
        }

        return $goodsReceipts;
    }

    public static function mapGoodsReceiptApproval($goodsReceipt) {
        $goodsReceipt->subtotal = $goodsReceipt->pendingApproval->subtotal;
        $goodsReceipt->tax_amount = $goodsReceipt->pendingApproval->tax_amount;
        $goodsReceipt->grand_total = $goodsReceipt->pendingApproval->grand_total;
        $goodsReceipt->goodsReceiptItems = $goodsReceipt->pendingApproval->approvalItems;

        return $goodsReceipt;
    }
}
