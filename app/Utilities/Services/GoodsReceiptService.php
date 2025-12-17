<?php

namespace App\Utilities\Services;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Utilities\Constant;
use Illuminate\Support\Facades\Auth;

class GoodsReceiptService
{
    public static function getBaseQueryIndex() {
        return GoodsReceipt::query()
            ->select(
                'goods_receipts.*',
                'branches.name AS branch_name',
                'branches.address AS branch_address',
                'warehouses.name AS warehouse_name',
                'suppliers.name AS supplier_name',
                'users.username AS user_name'
            )
            ->leftJoin('branches', 'branches.id', 'goods_receipts.branch_id')
            ->leftJoin('warehouses', 'warehouses.id', 'goods_receipts.warehouse_id')
            ->leftJoin('suppliers', 'suppliers.id', 'goods_receipts.supplier_id')
            ->leftJoin('users', 'users.id', 'goods_receipts.user_id');
    }

    public static function getBaseQueryExportItem() {
        return GoodsReceiptItem::query()
            ->select(
                'goods_receipt_items.*',
                'goods_receipts.number AS receipt_number',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'units.name AS unit_name'
            )
            ->join('goods_receipts', 'goods_receipts.id', 'goods_receipt_items.goods_receipt_id')
            ->join('products', 'products.id', 'goods_receipt_items.product_id')
            ->join('units', 'units.id', 'goods_receipt_items.unit_id')
            ->whereNull('goods_receipt_items.deleted_at')
            ->whereNull('goods_receipts.deleted_at');
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
        $goodsReceipt->outstanding_amount = $goodsReceipt->pendingApproval->grand_total - $goodsReceipt->payment_amount;
        $goodsReceipt->goodsReceiptItems = $goodsReceipt->pendingApproval->approvalItems;

        return $goodsReceipt;
    }

    public static function getGoodsReceiptItemById($id) {
        return GoodsReceiptItem::query()->findOrFail($id);
    }

    public static function getGoodsReceiptQuantityByGoodsReceiptProductIds($goodsReceiptId, $productIds) {
        $goodsReceiptQuantities = GoodsReceiptItem::query()
            ->where('goods_receipt_id', $goodsReceiptId)
            ->whereIn('product_id', $productIds)
            ->get();

        return $goodsReceiptQuantities;
    }

    public static function getLatestGoodsReceiptByProductId($productId) {
        return GoodsReceipt::query()
            ->whereHas('goodsReceiptItems', function($query) use ($productId) {
                $query->where('product_id', $productId)
                    ->whereNull('deleted_at');
            })
            ->whereNull('deleted_at')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->first();
    }

    public static function handleApprovalData($id, $approval) {
        $goodsReceipt = GoodsReceipt::query()->findOrFail($id);
        $status = $approval->type == Constant::APPROVAL_TYPE_EDIT
            ? Constant::GOODS_RECEIPT_STATUS_UPDATED
            : Constant::GOODS_RECEIPT_STATUS_CANCELLED;

        $goodsReceipt->update([
            'status' => $status,
        ]);

        foreach($approval->approvalItems as $approvalItem) {
            $productStock = ProductService::getProductStockQuery(
                $approvalItem->product_id,
                $goodsReceipt->warehouse_id
            );

            $currentStockLog = ProductService::findAndDeleteProductStockLog(
                $goodsReceipt->id,
                $approvalItem->product_id,
                $goodsReceipt->warehouse_id,
                Constant::PRODUCT_STOCK_LOG_TYPE_GOODS_RECEIPT
            );

            $currentWholesalePrice = $currentStockLog?->wholesale_price ?? 0;
            $currentRetailPrice = $currentStockLog?->retail_price ?? 0;

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
                        $goodsReceipt->id,
                        $goodsReceipt->date,
                        $goodsReceipt->warehouse_id,
                        $goodsReceipt->supplier_id,
                        $goodsReceipt->branch_id,
                        $approvalItem->total,
                        $approvalItem->price,
                        $currentWholesalePrice,
                        $currentRetailPrice
                    );
                } else {
                    ProductService::createProductStockLog(
                        $goodsReceipt->id,
                        $goodsReceipt->date,
                        $approvalItem->product_id,
                        $goodsReceipt->warehouse_id,
                        $productStock ? $productStock->stock - $receiptItem->actual_quantity : 0,
                        $approvalItem->actual_quantity,
                        $goodsReceipt->branch_id,
                        $goodsReceipt->supplier_id,
                        $approvalItem->total,
                        null,
                        false,
                        $approvalItem->price,
                        $currentWholesalePrice,
                        $currentRetailPrice,
                    );

                    $actualQuantity = $approvalItem->actual_quantity - $receiptItem->actual_quantity;
                    $productStock?->increment('stock', $actualQuantity);
                }
            }
        }

        if($approval->type == Constant::APPROVAL_TYPE_EDIT) {
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

                if ($receiptItem) {
                    $productStock?->decrement('stock', $receiptItem->actual_quantity);
                }
            });

            $goodsReceipt->goodsReceiptItems()->delete();
            foreach ($approval->approvalItems as $approvalItem) {
                $goodsReceipt->goodsReceiptItems()->create([
                    'product_id' => $approvalItem->product_id,
                    'unit_id' => $approvalItem->unit_id,
                    'quantity' => $approvalItem->quantity,
                    'actual_quantity' => $approvalItem->actual_quantity,
                    'price' => $approvalItem->price,
                    'wages' => $approvalItem->wages,
                    'shipping_cost' => $approvalItem->shipping_cost,
                    'cost_price' => $approvalItem->cost_price,
                    'total' => $approvalItem->total,
                ]);

                $latestGoodsReceipt = static::getLatestGoodsReceiptByProductId($approvalItem->product_id);

                if ($latestGoodsReceipt && $latestGoodsReceipt->id == $goodsReceipt->id) {
                    ProductService::updateProductPrice($approvalItem->product_id, $approvalItem->price);
                }
            }

            $goodsReceipt->update([
                'subtotal' => $approval->subtotal,
                'tax_amount' => $approval->tax_amount,
                'grand_total' => $approval->grand_total,
                'outstanding_amount' => $approval->grand_total - $goodsReceipt->payment_amount,
            ]);

            if ($goodsReceipt->status != Constant::GOODS_RECEIPT_STATUS_CANCELLED) {
                if ($goodsReceipt->outstanding_amount <= 0) {
                    $goodsReceipt->accountPayable()->update([
                        'status' => Constant::ACCOUNT_PAYABLE_STATUS_PAID,
                    ]);
                } else if ($goodsReceipt->payment_amount > 0) {
                    $goodsReceipt->accountPayable()->update([
                        'status' => Constant::ACCOUNT_PAYABLE_STATUS_ONGOING,
                    ]);
                } else {
                    $goodsReceipt->accountPayable()->update([
                        'status' => Constant::ACCOUNT_PAYABLE_STATUS_UNPAID,
                    ]);
                }
            } else {
                $goodsReceipt->accountPayable()->delete();
            }
        } else {
            AccountPayableService::deleteData($goodsReceipt->accountPayable);
            PurchaseReturnService::createAutoCancelApprovalData($goodsReceipt);
        }

        return true;
    }
}
