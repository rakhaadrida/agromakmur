<?php

namespace App\Utilities\Services;

use App\Models\PurchaseReturn;
use App\Models\SalesReturn;
use App\Utilities\Constant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseReturnService
{
    public static function getBaseQueryIndex() {
        return PurchaseReturn::query()
            ->select(
                'purchase_returns.*',
                'goods_receipts.number AS goods_receipt_number',
                'branches.name AS branch_name',
                'suppliers.name AS supplier_name',
                DB::raw('SUM(purchase_return_items.quantity) AS quantity'),
                DB::raw('SUM(purchase_return_items.received_quantity) AS received_quantity'),
                DB::raw('SUM(purchase_return_items.cut_bill_quantity) AS cut_bill_quantity'),
            )
            ->join('goods_receipts', 'goods_receipts.id', 'purchase_returns.goods_receipt_id')
            ->join('branches', 'branches.id', 'goods_receipts.branch_id')
            ->join('suppliers', 'suppliers.id', 'purchase_returns.supplier_id')
            ->leftJoin('purchase_return_items', 'purchase_return_items.purchase_return_id', 'purchase_returns.id')
            ->whereNull('purchase_return_items.deleted_at')
            ->groupBy('purchase_returns.id');
    }

    public static function createItemData($purchaseReturn, $request) {
        $totalReturnQuantity = 0;
        $totalReceivedQuantity = 0;
        $totalCutBillQuantity = 0;
        $productIds = $request->get('product_id', []);
        foreach ($productIds as $index => $productId) {
            if(!empty($productId)) {
                $itemId = $request->get('item_id')[$index];
                $unitId = $request->get('unit_id')[$index];
                $quantity = $request->get('quantity')[$index];
                $realQuantity = $request->get('real_quantity')[$index];
                $receivedQuantity = $request->get('received_quantity')[$index];
                $cutBillQuantity = $request->get('cut_bill_quantity')[$index];
                $actualQuantity = $quantity * $realQuantity;
                $actualReceivedQuantity = $receivedQuantity * $realQuantity;

                if($quantity > 0) {
                    $purchaseReturn->purchaseReturnItems()->create([
                        'goods_receipt_item_id' => $itemId,
                        'product_id' => $productId,
                        'unit_id' => $unitId,
                        'quantity' => $quantity,
                        'actual_quantity' => $actualQuantity,
                        'received_quantity' => $receivedQuantity,
                        'cut_bill_quantity' => $cutBillQuantity,
                    ]);

                    $totalReturnQuantity += $quantity;
                    $totalReceivedQuantity += $receivedQuantity;
                    $totalCutBillQuantity += $cutBillQuantity;

                    $returnWarehouse = WarehouseService::getReturnWarehouse();
                    $productStock = ProductService::getProductStockQuery(
                        $productId,
                        $returnWarehouse->id
                    );

                    ProductService::deleteProductStockLog(
                        $purchaseReturn->id,
                        $productId,
                        $returnWarehouse->id,
                        Constant::PRODUCT_STOCK_LOG_TYPE_PURCHASE_RETURN
                    );

                    $initialStock = $productStock ? $productStock->stock : 0;
                    $initialDelivered = $initialStock - $actualQuantity;

                    ProductService::createProductStockLog(
                        $purchaseReturn->id,
                        $purchaseReturn->date,
                        $productId,
                        $returnWarehouse->id,
                        $initialStock,
                        -$actualQuantity,
                        $purchaseReturn->goodsReceipt->branch_id,
                        $purchaseReturn->supplier_id,
                        0,
                        null,
                        true
                    );

                    if($actualReceivedQuantity > 0) {
                        ProductService::createProductStockLog(
                            $purchaseReturn->id,
                            $purchaseReturn->date,
                            $productId,
                            $returnWarehouse->id,
                            $initialDelivered,
                            $actualReceivedQuantity,
                            $purchaseReturn->goodsReceipt->branch_id,
                            $purchaseReturn->supplier_id,
                            0,
                            null,
                            true
                        );
                    }

                    $productStock?->decrement('stock', $actualQuantity - $actualReceivedQuantity);

                    if($cutBillQuantity > 0) {
                        $accountPayable = AccountPayableService::getAccountPayableByGoodsReceiptId($purchaseReturn->goods_receipt_id);

                        if($accountPayable) {
                            $goodsReceiptItem = GoodsReceiptService::getGoodsReceiptItemById($itemId);

                            $wages = ceil(($goodsReceiptItem->wages ?? 0) / $cutBillQuantity);
                            $shippingCost = ceil(($goodsReceiptItem->shipping_cost ?? 0) / $cutBillQuantity);
                            $totalExpenses = $wages + $shippingCost;
                            $total = ($cutBillQuantity * $goodsReceiptItem->price) + $totalExpenses;

                            $accountPayable->returns()->create([
                                'purchase_return_id' => $purchaseReturn->id,
                                'product_id' => $productId,
                                'unit_id' => $unitId,
                                'quantity' => $cutBillQuantity,
                                'actual_quantity' => $cutBillQuantity * $realQuantity,
                                'price' => $goodsReceiptItem->price,
                                'wages' => $wages,
                                'shipping_cost' => $shippingCost,
                                'total' => $total,
                            ]);
                        }
                    }
                }
            }
        }

        $totalRemainingQuantity = $totalReturnQuantity - $totalReceivedQuantity - $totalCutBillQuantity;

        $receiptStatus = Constant::PURCHASE_RETURN_RECEIPT_STATUS_ACTIVE;
        if($totalRemainingQuantity == 0) {
            $receiptStatus = Constant::PURCHASE_RETURN_RECEIPT_STATUS_COMPLETED;
        } else if($totalReceivedQuantity > 0 || $totalCutBillQuantity > 0) {
            $receiptStatus = Constant::PURCHASE_RETURN_RECEIPT_STATUS_ONGOING;
        }

        $purchaseReturn->update([
            'receipt_status' => $receiptStatus
        ]);

        return true;
    }

    public static function deleteItemData($purchaseReturnItems) {
        $returnWarehouse = WarehouseService::getReturnWarehouse();

        foreach ($purchaseReturnItems as $item) {
            $realQuantity = $item->actual_quantity / $item->quantity;
            $actualReceivedQuantity = $item->received_quantity * $realQuantity;

            $productStock = ProductService::getProductStockQuery(
                $item->product_id,
                $returnWarehouse->id
            );

            $productStock?->increment('stock', $item->actual_quantity - $actualReceivedQuantity);

            $item->delete();
        }
    }

    public static function handleApprovalData($id) {
        $purchaseReturn = PurchaseReturn::query()->findOrFail($id);
        $purchaseReturn->update([
            'status' => Constant::PURCHASE_RETURN_STATUS_CANCELLED
        ]);

        $returnWarehouse = WarehouseService::getReturnWarehouse();
        foreach($purchaseReturn->purchaseReturnItems as $purchaseReturnItem) {
            $productStock = ProductService::getProductStockQuery(
                $purchaseReturnItem->product_id,
                $returnWarehouse->id
            );

            $returnWarehouse = WarehouseService::getReturnWarehouse();

            ProductService::deleteProductStockLog(
                $purchaseReturn->id,
                $purchaseReturnItem->product_id,
                $returnWarehouse->id,
                Constant::PRODUCT_STOCK_LOG_TYPE_PURCHASE_RETURN
            );

            $realQuantity = $purchaseReturnItem->actual_quantity / $purchaseReturnItem->quantity;
            $actualReceivedQuantity = $purchaseReturnItem->received_quantity * $realQuantity;

            $productStock?->increment('stock', $purchaseReturnItem->actual_quantity - $actualReceivedQuantity);
        }

        $purchaseReturn->accountPayableReturns()->delete();

        return true;
    }

    public static function createAutoCancelApprovalData($salesOrder) {
        $returnWarehouse = WarehouseService::getReturnWarehouse();
        $salesReturns = SalesReturn::query()
            ->where('sales_order_id', $salesOrder->id)
            ->get();

        foreach($salesReturns as $salesReturn) {
            if($salesReturn->status == Constant::SALES_RETURN_STATUS_CANCELLED) {
                continue;
            }

            $salesReturn->update([
                'status' => Constant::SALES_RETURN_STATUS_CANCELLED
            ]);

            ApprovalService::deleteData($salesReturn->approvals);

            $approval = ApprovalService::createData(
                $salesReturn,
                $salesReturn->salesReturnItems,
                Constant::APPROVAL_TYPE_CANCEL,
                Constant::APPROVAL_STATUS_APPROVED,
                'Auto cancel by system due to sales order cancellation'
            );

            $approval->update([
                'updated_by' => Auth::user()->id
            ]);

            foreach($salesReturn->salesReturnItems as $salesReturnItem) {
                $productStock = ProductService::getProductStockQuery(
                    $salesReturnItem->product_id,
                    $returnWarehouse->id
                );

                $realQuantity = $salesReturnItem->actual_quantity * $salesReturnItem->quantity;
                $actualDeliveredQuantity = $salesReturnItem->delivered_quantity * $realQuantity;

                $productStock?->decrement('stock', $salesReturnItem->actualQuantity - $actualDeliveredQuantity);
            }
        }
    }
}
