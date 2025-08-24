<?php

namespace App\Utilities\Services;

use App\Models\GoodsReceipt;
use App\Utilities\Constant;
use Illuminate\Support\Facades\Auth;

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

    public static function handleApprovalData($id, $approval) {
        $goodsReceipt = GoodsReceipt::query()->findOrFail($id);
        $status = $approval->type == Constant::APPROVAL_TYPE_EDIT
            ? Constant::GOODS_RECEIPT_STATUS_UPDATED
            : Constant::GOODS_RECEIPT_STATUS_CANCELLED;

        $goodsReceipt->update([
            'status' => $status,
            'updated_by' => Auth::user()->id
        ]);

        foreach($approval->approvalItems as $approvalItem) {
            $productStock = ProductService::getProductStockQuery(
                $approvalItem->product_id,
                $goodsReceipt->warehouse_id
            );

            if($approval->type == Constant::APPROVAL_TYPE_CANCEL) {
                $productStock?->decrement('stock', $approvalItem->actual_quantity);
            } else {
                $receiptItem = $goodsReceipt->goodsReceiptItems
                    ->where('product_id', $approvalItem->product_id)
                    ->first();

                if(!$receiptItem) {
                    ProductService::updateProductStockIncrement(
                        $approvalItem->product_id,
                        $productStock,
                        $approvalItem->actual_quantity,
                        $goodsReceipt->warehouse_id
                    );
                } else {
                    $actualQuantity = $approvalItem->actual_quantity - $receiptItem->actual_quantity;
                    $productStock?->increment('stock', $actualQuantity);
                }
            }
        }

        $approvalItemProductIds = $approval->approvalItems->pluck('product_id');
        $receiptItemProductIds = $goodsReceipt->goodsReceiptItems->pluck('product_id');

        $missingReceiptItemIds = $receiptItemProductIds->diff($approvalItemProductIds);
        $missingReceiptItemIds->each(function ($itemId) use ($goodsReceipt) {
            $productStock = ProductService::getProductStockQuery(
                $itemId,
                $goodsReceipt->warehouse_id
            );

            $receiptItem = $goodsReceipt->goodsReceiptItems
                ->where('product_id', $itemId)
                ->first();

            if($receiptItem) {
                $productStock?->decrement('stock', $receiptItem->actual_quantity);
            }
        });

        $goodsReceipt->goodsReceiptItems()->delete();
        foreach($approval->approvalItems as $approvalItem) {
            $goodsReceipt->goodsReceiptItems()->create([
                'product_id' => $approvalItem->product_id,
                'unit_id' => $approvalItem->unit_id,
                'quantity' => $approvalItem->quantity,
                'actual_quantity' => $approvalItem->actual_quantity,
                'price' => $approvalItem->price,
                'wages' => $approvalItem->wages,
                'shipping_cost' => $approvalItem->shipping_cost,
                'total' => $approvalItem->total,
            ]);
        }

        $goodsReceipt->update([
            'subtotal' => $approval->subtotal,
            'tax_amount' => $approval->tax_amount,
            'grand_total' => $approval->grand_total,
        ]);

        return true;
    }
}
