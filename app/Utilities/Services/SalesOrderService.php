<?php

namespace App\Utilities\Services;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Utilities\Constant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesOrderService
{
    public static function getBaseQueryIndex() {
        return SalesOrder::query()
            ->select(
                'sales_orders.*',
                'customers.name AS customer_name',
                'customers.address AS customer_address',
                'marketings.name AS marketing_name',
                'users.username AS user_name'
            )
            ->leftJoin('customers', 'customers.id', 'sales_orders.customer_id')
            ->leftJoin('marketings', 'marketings.id', 'sales_orders.marketing_id')
            ->leftJoin('users', 'users.id', 'sales_orders.user_id');
    }

    public static function mapSalesOrderIndex($salesOrders, $isIndexEdit = false) {
        foreach ($salesOrders as $salesOrder) {
            if(isWaitingApproval($salesOrder->status) && isApprovalTypeEdit($salesOrder->pendingApproval->type)) {
                static::mapSalesOrderApproval($salesOrder);
            }
        }

        if(!$isIndexEdit) {
            $salesOrders = $salesOrders->sortBy(function ($salesOrder) {
                return $salesOrder->date;
            })->values();
        } else {
            $salesOrders = $salesOrders->sortByDesc(function ($salesOrder) {
                return [$salesOrder->date, $salesOrder->id];
            })->values();
        }

        return $salesOrders;
    }

    public static function mapSalesOrderApproval($salesOrder) {
        $salesOrder->date = $salesOrder->pendingApproval->subject_date;
        $salesOrder->customer_id = $salesOrder->pendingApproval->customer_id;
        $salesOrder->customer_name = $salesOrder->pendingApproval->customer->name;
        $salesOrder->marketing_id = $salesOrder->pendingApproval->marketing_id;
        $salesOrder->marketing_name = $salesOrder->pendingApproval->marketing->name;
        $salesOrder->tempo = $salesOrder->pendingApproval->tempo;
        $salesOrder->subtotal = $salesOrder->pendingApproval->subtotal;
        $salesOrder->discount_amount = $salesOrder->pendingApproval->discount_amount;
        $salesOrder->tax_amount = $salesOrder->pendingApproval->tax_amount;
        $salesOrder->grand_total = $salesOrder->pendingApproval->grand_total;
        $salesOrder->salesOrderItems = $salesOrder->pendingApproval->approvalItems;

        return $salesOrder;
    }

    public static function mapSalesOrderItemDetail($salesOrderItems) {
        return $salesOrderItems
            ->groupBy('product_id')
            ->map(function ($items, $productId) {
                $totalDiscount = 100;
                $discount = str_replace(',', '.', $items->first()->discount);
                $arrayDiscounts = explode('+', $discount);

                foreach($arrayDiscounts as $arrayDiscount) {
                    $totalDiscount -= ($arrayDiscount * $totalDiscount) / 100;
                }

                $discountPercentage = number_format((($totalDiscount - 100) * -1), 2, ",", "");

                return (object) [
                    'id' => $items->first()->id,
                    'product_id' => $productId,
                    'product_sku' => $items->first()->product->sku,
                    'product_name' => $items->first()->product->name,
                    'product_unit_id' => $items->first()->product->unit_id,
                    'warehouse_ids' => $items->pluck('warehouse_id')->unique()->implode(','),
                    'warehouse_stocks' => $items->pluck('quantity')->unique()->implode(','),
                    'quantity' => $items->sum('quantity'),
                    'actual_quantity' => $items->sum('actual_quantity'),
                    'unit_id' => $items->first()->unit_id,
                    'unit_name' => $items->first()->unit->name,
                    'price_id' => $items->first()->price_id,
                    'price' => $items->first()->price,
                    'total' => $items->sum('total'),
                    'discount' => $items->first()->discount,
                    'discount_percentage' => $discountPercentage,
                    'discount_amount' => $items->sum('discount_amount'),
                    'final_amount' => $items->sum('final_amount'),
                ];
            })
            ->values();
    }

    public static function getSalesOrderQuantityBySalesOrderProductIds($salesOrderId, $productIds) {
        $salesOrderQuantities = SalesOrderItem::query()
            ->select(
                'product_id',
                DB::raw('SUM(quantity) AS quantity')
            )
            ->where('sales_order_id', $salesOrderId)
            ->whereIn('product_id', $productIds)
            ->groupBy('product_id')
            ->get();

        return $salesOrderQuantities;
    }

    public static function getSalesOrderItemById($id) {
        return SalesOrderItem::query()->findOrFail($id);
    }

    public static function handleApprovalData($id, $approval) {
        $salesOrder = SalesOrder::query()->findOrFail($id);
        $status = $approval->type == Constant::APPROVAL_TYPE_EDIT
            ? Constant::SALES_ORDER_STATUS_UPDATED
            : Constant::SALES_ORDER_STATUS_CANCELLED;

        $salesOrder->update([
            'status' => $status
        ]);

        foreach($approval->approvalItems as $approvalItem) {
            $productStock = ProductService::getProductStockQuery(
                $approvalItem->product_id,
                $approvalItem->warehouse_id
            );

            if($approval->type == Constant::APPROVAL_TYPE_CANCEL) {
                $productStock?->increment('stock', $approvalItem->actual_quantity);
            } else {
                $orderItem = $salesOrder->salesOrderItems
                    ->where('product_id', $approvalItem->product_id)
                    ->first();

                if(!$orderItem) {
                    $productStock?->decrement('stock', $approvalItem->actual_quantity);
                } else {
                    $actualQuantity = $approvalItem->actual_quantity - $orderItem->actual_quantity;
                    $productStock?->increment('stock', $actualQuantity);
                }
            }
        }

        if($approval->type == Constant::APPROVAL_TYPE_EDIT) {
            $approvalItemProductIds = $approval->approvalItems->pluck('product_id');
            $orderItemProductIds = $salesOrder->salesOrderItems->pluck('product_id');

            $missingOrderItemIds = $orderItemProductIds->diff($approvalItemProductIds);
            $missingOrderItems = $salesOrder->salesOrderItems->whereIn('product_id', $missingOrderItemIds);

            foreach ($missingOrderItems as $missingOrderItem) {
                $productStock = ProductService::getProductStockQuery(
                    $missingOrderItem->product_id,
                    $missingOrderItem->warehouse_id
                );

                if ($productStock) {
                    $productStock->increment('stock', $missingOrderItem->actual_quantity);
                }
            }

            $salesOrder->salesOrderItems()->delete();
            foreach ($approval->approvalItems as $approvalItem) {
                $salesOrder->salesOrderItems()->create([
                    'product_id' => $approvalItem->product_id,
                    'warehouse_id' => $approvalItem->warehouse_id,
                    'unit_id' => $approvalItem->unit_id,
                    'quantity' => $approvalItem->quantity,
                    'actual_quantity' => $approvalItem->actual_quantity,
                    'price_id' => $approvalItem->price_id,
                    'price' => $approvalItem->price,
                    'total' => $approvalItem->total,
                    'discount' => $approvalItem->discount,
                    'discount_amount' => $approvalItem->discount_amount,
                    'final_amount' => $approvalItem->final_amount,
                ]);
            }

            $salesOrder->update([
                'date' => $approval->subject_date ?: $salesOrder->date,
                'customer_id' => $approval->customer_id ?: $salesOrder->customer_id,
                'marketing_id' => $approval->marketing_id ?: $salesOrder->marketing_id,
                'tempo' => $approval->tempo ?: $salesOrder->tempo,
                'subtotal' => $approval->subtotal,
                'discount_amount' => $approval->discount_amount,
                'tax_amount' => $approval->tax_amount,
                'grand_total' => $approval->grand_total,
            ]);
        } else {
            AccountReceivableService::deleteData($salesOrder->accountReceivable);
            DeliveryOrderService::createAutoCancelApprovalData($salesOrder);
            SalesReturnService::createAutoCancelApprovalData($salesOrder);
        }

        return true;
    }
}
