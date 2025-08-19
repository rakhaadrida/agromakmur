<?php

namespace App\Utilities\Services;

use App\Models\AccountReceivable;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Utilities\Constant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DeliveryOrderService
{
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
            'warehouse_id' => $salesOrderItem->warehouse_id,
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
}
