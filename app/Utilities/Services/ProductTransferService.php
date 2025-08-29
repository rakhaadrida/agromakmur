<?php

namespace App\Utilities\Services;

use App\Models\ProductTransfer;
use App\Utilities\Constant;

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

    public static function handleApprovalData($id) {
        $productTransfer = ProductTransfer::query()->findOrFail($id);
        $productTransfer->update([
            'status' => Constant::PRODUCT_TRANSFER_STATUS_CANCELLED
        ]);

        foreach($productTransfer->productTransferItems as $productTransferItem) {
            $sourceWarehouseStock = ProductService::getProductStockQuery(
                $productTransferItem->product_id,
                $productTransferItem->source_warehouse_id
            );

            $sourceWarehouseStock?->increment('stock', $productTransferItem->actual_quantity);

            $destinationWarehouseStock = ProductService::getProductStockQuery(
                $productTransferItem->product_id,
                $productTransferItem->destination_warehouse_id
            );

            $destinationWarehouseStock?->decrement('stock', $productTransferItem->actual_quantity);
        }

        return true;
    }
}
