<?php

namespace App\Utilities\Services;

use App\Models\SalesOrder;

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
}
