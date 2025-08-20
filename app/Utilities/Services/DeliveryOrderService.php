<?php

namespace App\Utilities\Services;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Utilities\Constant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
}
