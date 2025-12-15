<?php

namespace App\Utilities\Services;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Utilities\Constant;
use Illuminate\Support\Facades\DB;

class SalesOrderService
{
    public static function getBaseQueryIndex() {
        return SalesOrder::query()
            ->select(
                'sales_orders.*',
                'branches.name AS branch_name',
                'branches.address AS branch_address',
                'branches.phone_number AS branch_phone_number',
                'customers.name AS customer_name',
                'customers.address AS customer_address',
                'marketings.name AS marketing_name',
                'users.username AS user_name'
            )
            ->leftJoin('branches', 'branches.id', 'sales_orders.branch_id')
            ->leftJoin('customers', 'customers.id', 'sales_orders.customer_id')
            ->leftJoin('marketings', 'marketings.id', 'sales_orders.marketing_id')
            ->leftJoin('users', 'users.id', 'sales_orders.user_id');
    }

    public static function getBaseQueryExportItem() {
        return SalesOrderItem::query()
            ->select(
                'sales_order_items.*',
                'sales_orders.number AS order_number',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'units.name AS unit_name'
            )
            ->join('sales_orders', 'sales_orders.id', 'sales_order_items.sales_order_id')
            ->join('products', 'products.id', 'sales_order_items.product_id')
            ->join('units', 'units.id', 'sales_order_items.unit_id')
            ->whereNull('sales_order_items.deleted_at')
            ->whereNull('sales_orders.deleted_at');
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
        $salesOrder->tax_amount = $salesOrder->pendingApproval->tax_amount;
        $salesOrder->grand_total = $salesOrder->pendingApproval->grand_total;
        $salesOrder->salesOrderItems = $salesOrder->pendingApproval->approvalItems;

        return $salesOrder;
    }

    public static function mapSalesOrderItemDetail($salesOrderItems) {
        return $salesOrderItems
            ->groupBy('product_id')
            ->map(function ($items, $productId) {
                $realQuantity = $items->sum('actual_quantity') / $items->sum('quantity');
                $actualPrice = $items->first()->price / $realQuantity;

                return (object) [
                    'id' => $items->first()->id,
                    'product_id' => $productId,
                    'product_sku' => $items->first()->product->sku,
                    'product_name' => $items->first()->product->name,
                    'product_unit_id' => $items->first()->product->unit_id,
                    'product_unit_name' => $items->first()->product->unit->name,
                    'warehouse_ids' => $items->pluck('warehouse_id')->unique()->implode(','),
                    'warehouse_stocks' => $items->pluck('quantity')->unique()->implode(','),
                    'quantity' => $items->sum('quantity'),
                    'actual_quantity' => $items->sum('actual_quantity'),
                    'unit_id' => $items->first()->unit_id,
                    'unit_name' => $items->first()->unit->name,
                    'price_id' => $items->first()->price_id,
                    'price' => $items->first()->price,
                    'actual_price' => $actualPrice,
                    'total' => $items->sum('total'),
                ];
            })
            ->values();
    }

    public static function mapSalesOrderItemExport($salesOrderItems) {
        return $salesOrderItems
            ->groupBy(function ($item) {
                return $item->sales_order_id . '-' . $item->product_id;
            })
            ->map(function ($items, $key) {
                $first = $items->first();

                $realQuantity = $items->sum('actual_quantity') / max(1, $items->sum('quantity'));
                $actualPrice = $first->price / $realQuantity;

                return (object) [
                    'id'                  => $first->id,
                    'sales_order_id'      => $first->sales_order_id,
                    'order_number'        => $first->salesOrder->number,
                    'product_id'          => $first->product_id,
                    'product_sku'         => $first->product->sku,
                    'product_name'        => $first->product->name,
                    'quantity'            => $items->sum('quantity'),
                    'actual_quantity'     => $items->sum('actual_quantity'),
                    'unit_id'             => $first->unit_id,
                    'unit_name'           => $first->unit->name,
                    'price_id'            => $first->price_id,
                    'price'               => $first->price,
                    'actual_price'        => $actualPrice,
                    'total'               => $items->sum('total'),
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

            ProductService::deleteProductStockLog(
                $salesOrder->id,
                $approvalItem->product_id,
                $approvalItem->warehouse_id,
                Constant::PRODUCT_STOCK_LOG_TYPE_SALES_ORDER
            );

            if($approval->type == Constant::APPROVAL_TYPE_CANCEL) {
                $productStock?->increment('stock', $approvalItem->actual_quantity);
            } else {
                $orderItem = $salesOrder->salesOrderItems
                    ->where('product_id', $approvalItem->product_id)
                    ->first();

                $initialStock = $productStock ? $productStock->stock + ($orderItem ? $orderItem->actual_quantity : 0) : 0;

                ProductService::createProductStockLog(
                    $salesOrder->id,
                    $salesOrder->date,
                    $approvalItem->product_id,
                    $approvalItem->warehouse_id,
                    $initialStock,
                    -$approvalItem->actual_quantity,
                    $salesOrder->branch_id,
                    null,
                    $approvalItem->total,
                    $approval->customer_id
                );

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
                ]);
            }

            $salesOrder->update([
                'date' => $approval->subject_date ?: $salesOrder->date,
                'customer_id' => $approval->customer_id ?: $salesOrder->customer_id,
                'marketing_id' => $approval->marketing_id ?: $salesOrder->marketing_id,
                'tempo' => $approval->tempo ?: $salesOrder->tempo,
                'subtotal' => $approval->subtotal,
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
