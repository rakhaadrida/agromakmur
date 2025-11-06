<?php

namespace App\Utilities\Services;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Utilities\Constant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryOrderService
{
    public static function getBaseQueryIndex() {
        return DeliveryOrder::query()
            ->select(
                'delivery_orders.*',
                'sales_orders.number AS sales_order_number',
                'customers.name AS customer_name',
                'users.username AS user_name'
            )
            ->leftJoin('sales_orders', 'sales_orders.id', 'delivery_orders.sales_order_id')
            ->leftJoin('customers', 'customers.id', 'delivery_orders.customer_id')
            ->leftJoin('users', 'users.id', 'delivery_orders.user_id');
    }

    public static function getBaseQueryExportItem() {
        return DeliveryOrderItem::query()
            ->select(
                'delivery_order_items.*',
                'delivery_orders.number AS delivery_number',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'units.name AS unit_name'
            )
            ->join('delivery_orders', 'delivery_orders.id', 'delivery_order_items.delivery_order_id')
            ->join('products', 'products.id', 'delivery_order_items.product_id')
            ->join('units', 'units.id', 'delivery_order_items.unit_id')
            ->whereNull('delivery_order_items.deleted_at')
            ->whereNull('delivery_orders.deleted_at');
    }

    public static function getAdditionalQueryIndex($baseQuery) {
        return $baseQuery
            ->addSelect(DB::raw('delivery_order_items.quantity AS total_quantity'))
            ->leftJoinSub(
                DB::table('delivery_order_items')
                    ->select(
                        'delivery_order_items.delivery_order_id',
                        DB::raw('SUM(delivery_order_items.quantity) AS quantity')
                    )
                    ->whereNull('delivery_order_items.deleted_at')
                    ->groupBy('delivery_order_items.delivery_order_id'),
                'delivery_order_items',
                'delivery_orders.id',
                'delivery_order_items.delivery_order_id'
            );
    }

    public static function createData($salesOrder) {
        return DeliveryOrder::create([
            'sales_order_id' => $salesOrder->id,
            'customer_id' => $salesOrder->customer_id,
            'number' => static::generateOrderNumber(),
            'date' => $salesOrder->delivery_date,
            'address' => $salesOrder->customer->address,
            'status' => Constant::DELIVERY_ORDER_STATUS_ACTIVE,
            'user_id' => Auth::user()->id
        ]);
    }

    public static function createItemData($deliveryOrder, $salesOrderItem) {
        DeliveryOrderItem::create([
            'delivery_order_id' => $deliveryOrder->id,
            'sales_order_item_id' => $salesOrderItem->id,
            'product_id' => $salesOrderItem->product_id,
            'unit_id' => $salesOrderItem->unit_id,
            'quantity' => $salesOrderItem->quantity,
            'actual_quantity' => $salesOrderItem->actual_quantity,
        ]);

        return true;
    }

    public static function generateOrderNumber() {
        $date = Carbon::now();

        $lastNumber = DeliveryOrder::query()
            ->selectRaw('MAX(number) AS number')
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->first();

        $lastIncrement = (int) substr($lastNumber->number, 7, 4);
        $lastIncrement++;

        $numberFormat = 'DO-' . $date->format('y') . $date->format('m');
        $newNumber = $numberFormat . sprintf('%04s', $lastIncrement);

        return $newNumber;
    }

    public static function mapDeliveryOrderIndex($deliveryOrders) {
        foreach ($deliveryOrders as $deliveryOrder) {
            if(isWaitingApproval($deliveryOrder->status) && isApprovalTypeEdit($deliveryOrder->pendingApproval->type)) {
                static::mapDeliveryOrderApproval($deliveryOrder);
            }
        }

        return $deliveryOrders;
    }

    public static function mapDeliveryOrderApproval($deliveryOrder) {
        $deliveryOrder->deliveryOrderItems = $deliveryOrder->pendingApproval->approvalItems;

        return $deliveryOrder;
    }

    public static function getDeliveryQuantityBySalesOrderProductIds($salesOrderId, $productIds) {
        $deliveryOrderIds = DeliveryOrder::query()
            ->where('sales_order_id', $salesOrderId)
            ->pluck('id')
            ->toArray();

        $deliveryOrderQuantities = DeliveryOrderItem::query()
            ->select(
                'product_id',
                DB::raw('SUM(quantity) AS quantity')
            )
            ->whereIn('delivery_order_id', $deliveryOrderIds)
            ->whereIn('product_id', $productIds)
            ->groupBy('product_id')
            ->get();

        return $deliveryOrderQuantities;
    }

    public static function handleApprovalData($id, $approval) {
        $deliveryOrder = DeliveryOrder::query()->findOrFail($id);
        $status = $approval->type == Constant::APPROVAL_TYPE_EDIT
            ? Constant::DELIVERY_ORDER_STATUS_UPDATED
            : Constant::DELIVERY_ORDER_STATUS_CANCELLED;

        $deliveryOrder->update([
            'status' => $status
        ]);

        foreach($approval->approvalItems as $approvalItem) {
            $deliveryItem = $deliveryOrder->deliveryOrderItems
                ->where('product_id', $approvalItem->product_id)
                ->first();

            if($deliveryItem) {
                $deliveryItem->update([
                    'quantity' => $approvalItem->quantity,
                    'actual_quantity' => $approvalItem->actual_quantity,
                ]);
            }
        }

        return true;
    }

    public static function createAutoCancelApprovalData($salesOrder) {
        $deliveryOrders = DeliveryOrder::query()
            ->where('sales_order_id', $salesOrder->id)
            ->get();

        foreach($deliveryOrders as $deliveryOrder) {
            if($deliveryOrder->status == Constant::DELIVERY_ORDER_STATUS_CANCELLED) {
                continue;
            }

            $deliveryOrder->update([
                'status' => Constant::DELIVERY_ORDER_STATUS_CANCELLED
            ]);

            ApprovalService::deleteData($deliveryOrder->approvals);

            $approval = ApprovalService::createData(
                $deliveryOrder,
                $deliveryOrder->deliveryOrderItems,
                Constant::APPROVAL_TYPE_CANCEL,
                Constant::APPROVAL_STATUS_APPROVED,
                'Auto cancel by system due to sales order cancellation'
            );

            $approval->update([
                'updated_by' => Auth::user()->id
            ]);
        }
    }
}
