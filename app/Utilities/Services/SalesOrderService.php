<?php

namespace App\Utilities\Services;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Support\Facades\DB;

class SalesOrderService
{
    public static function getBaseQueryIndex() {
        return SalesOrder::query()
            ->select(
                'sales_orders.*',
                'customers.name AS customer_name',
                'marketings.name AS marketing_name',
                'users.username AS user_name'
            )
            ->leftJoin('customers', 'customers.id', 'sales_orders.customer_id')
            ->leftJoin('marketings', 'marketings.id', 'sales_orders.marketing_id')
            ->leftJoin('users', 'users.id', 'sales_orders.user_id');
    }

    public static function mapSalesOrderIndex($salesOrders) {
        foreach ($salesOrders as $salesOrder) {
            if(isWaitingApproval($salesOrder->status) && isApprovalTypeEdit($salesOrder->pendingApproval->type)) {
                static::mapSalesOrderApproval($salesOrder);
            }
        }

        return $salesOrders;
    }

    public static function mapSalesOrderApproval($salesOrder) {
        $salesOrder->subtotal = $salesOrder->pendingApproval->subtotal;
        $salesOrder->tax_amount = $salesOrder->pendingApproval->tax_amount;
        $salesOrder->grand_total = $salesOrder->pendingApproval->grand_total;
        $salesOrder->sales_order_items = $salesOrder->pendingApproval->approvalItems;

        return $salesOrder;
    }

    public static function mapSalesOrderItemDetail($salesOrderItems) {
        return $salesOrderItems
            ->groupBy('product_id')
            ->map(function ($items, $productId) {
                return (object) [
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
                    'discount_amount' => $items->sum('discount_amount'),
                    'final_amount' => $items->sum('final_amount'),
                ];
            })
            ->values();
    }
}
