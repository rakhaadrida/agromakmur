<?php

namespace App\Utilities\Services;

use App\Models\SalesReturn;
use App\Utilities\Constant;
use Illuminate\Support\Facades\DB;

class SalesReturnService
{
    public static function getBaseQueryIndex() {
        return SalesReturn::query()
            ->select(
                'sales_returns.*',
                'sales_orders.number AS sales_order_number',
                'customers.name AS customer_name',
                DB::raw('SUM(sales_return_items.quantity) AS quantity'),
                DB::raw('SUM(sales_return_items.delivered_quantity) AS delivered_quantity'),
                DB::raw('SUM(sales_return_items.cut_bill_quantity) AS cut_bill_quantity'),
            )
            ->join('sales_orders', 'sales_orders.id', 'sales_returns.sales_order_id')
            ->join('customers', 'customers.id', 'sales_returns.customer_id')
            ->leftJoin('sales_return_items', 'sales_return_items.sales_return_id', 'sales_returns.id')
            ->whereNull('sales_return_items.deleted_at')
            ->groupBy('sales_returns.id');
    }

    public static function createItemData($salesReturn, $request) {
        $totalReturnQuantity = 0;
        $totalDeliveredQuantity = 0;
        $totalCutBillQuantity = 0;
        $productIds = $request->get('product_id', []);
        foreach ($productIds as $index => $productId) {
            if(!empty($productId)) {
                $itemId = $request->get('item_id')[$index];
                $unitId = $request->get('unit_id')[$index];
                $quantity = $request->get('quantity')[$index];
                $realQuantity = $request->get('real_quantity')[$index];
                $deliveredQuantity = $request->get('delivered_quantity')[$index];
                $cutBillQuantity = $request->get('cut_bill_quantity')[$index];
                $actualQuantity = $quantity * $realQuantity;
                $actualDeliveredQuantity = $deliveredQuantity * $realQuantity;

                if($quantity > 0) {
                    $salesReturn->salesReturnItems()->create([
                        'sales_order_item_id' => $itemId,
                        'product_id' => $productId,
                        'unit_id' => $unitId,
                        'quantity' => $quantity,
                        'actual_quantity' => $actualQuantity,
                        'delivered_quantity' => $deliveredQuantity,
                        'cut_bill_quantity' => $cutBillQuantity,
                    ]);

                    $totalReturnQuantity += $quantity;
                    $totalDeliveredQuantity += $deliveredQuantity;
                    $totalCutBillQuantity += $cutBillQuantity;

                    $returnWarehouse = WarehouseService::getReturnWarehouse();
                    $productStock = ProductService::getProductStockQuery(
                        $productId,
                        $returnWarehouse->id
                    );

                    $productStock?->increment('stock', $actualQuantity - $actualDeliveredQuantity);

                    if($cutBillQuantity > 0) {
                        $accountReceivable = AccountReceivableService::getAccountReceivableBySalesOrderId($salesReturn->sales_order_id);

                        if($accountReceivable) {
                            $salesOrderItem = SalesOrderService::getSalesOrderItemById($itemId);
                            $total = $salesOrderItem->price * $cutBillQuantity;
                            $discountPercentage = CommonService::calculateDiscountPercentage($salesOrderItem->discount);
                            $discountAmount = ceil(($total * $discountPercentage) / 100);
                            $finalAmount = ceil($total - $discountAmount);

                            $accountReceivable->returns()->create([
                                'sales_return_id' => $salesReturn->id,
                                'product_id' => $productId,
                                'unit_id' => $unitId,
                                'quantity' => $cutBillQuantity,
                                'actual_quantity' => $cutBillQuantity * $realQuantity,
                                'price_id' => $salesOrderItem->price_id,
                                'price' => $salesOrderItem->price,
                                'total' => $total,
                                'discount' => $salesOrderItem->discount,
                                'discount_amount' => $discountAmount,
                                'final_amount' => $finalAmount,
                            ]);
                        }
                    }
                }
            }
        }

        $totalRemainingQuantity = $totalReturnQuantity - $totalDeliveredQuantity - $totalCutBillQuantity;

        $deliveryStatus = Constant::SALES_RETURN_DELIVERY_STATUS_ACTIVE;
        if($totalRemainingQuantity == 0) {
            $deliveryStatus = Constant::SALES_RETURN_DELIVERY_STATUS_COMPLETED;
        } else if($totalDeliveredQuantity > 0 || $totalCutBillQuantity > 0) {
            $deliveryStatus = Constant::SALES_RETURN_DELIVERY_STATUS_ONGOING;
        }

        $salesReturn->update([
            'delivery_status' => $deliveryStatus
        ]);

        return true;
    }

    public static function deleteItemData($salesReturnItems) {
        foreach ($salesReturnItems as $item) {
            $realQuantity = $item->actual_quantity * $item->quantity;
            $actualDeliveredQuantity = $item->delivered_quantity * $realQuantity;

            $returnWarehouse = WarehouseService::getReturnWarehouse();
            $productStock = ProductService::getProductStockQuery(
                $item->product_id,
                $returnWarehouse->id
            );

            $productStock?->decrement('stock', $item->actualQuantity - $actualDeliveredQuantity);

            $item->delete();
        }
    }
}
