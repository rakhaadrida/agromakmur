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
                'users.username AS user_name'
            )
            ->leftJoin('customers', 'customers.id', 'sales_orders.customer_id')
            ->leftJoin('users', 'users.id', 'sales_orders.user_id');
    }

    public static function getBaseQuerySalesOrderItem($salesOrderId) {
        return SalesOrderItem::query()
            ->select(
                'sales_order_items.*',
                DB::raw('SUM(quantity) AS quantity'),
                DB::raw('SUM(discount_amount) AS discount_amount'),
            )
            ->where('sales_order_id', $salesOrderId)
            ->groupBy('product_id');
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
}
