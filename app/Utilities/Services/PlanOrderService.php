<?php

namespace App\Utilities\Services;

use App\Models\PlanOrder;
use App\Models\PlanOrderItem;

class PlanOrderService
{
    public static function getBaseQueryIndex() {
        return PlanOrder::query()
            ->select(
                'plan_orders.*',
                'branches.name AS branch_name',
                'branches.address AS branch_address',
                'branches.phone_number AS branch_phone_number',
                'suppliers.name AS supplier_name',
                'suppliers.address AS supplier_address',
                'users.username AS user_name'
            )
            ->leftJoin('branches', 'branches.id', 'plan_orders.branch_id')
            ->leftJoin('suppliers', 'suppliers.id', 'plan_orders.supplier_id')
            ->leftJoin('users', 'users.id', 'plan_orders.user_id');
    }

    public static function getBaseQueryExportItem() {
        return PlanOrderItem::query()
            ->select(
                'plan_order_items.*',
                'plan_orders.number AS plan_order_number',
                'products.sku AS product_sku',
                'products.name AS product_name',
                'units.name AS unit_name'
            )
            ->join('plan_orders', 'plan_orders.id', 'plan_order_items.plan_order_id')
            ->join('products', 'products.id', 'plan_order_items.product_id')
            ->join('units', 'units.id', 'plan_order_items.unit_id')
            ->whereNull('plan_order_items.deleted_at')
            ->whereNull('plan_orders.deleted_at');
    }

    public static function createItemData($planOrder, $request, $isUpdate = false): bool {
        if($isUpdate) {
            $planOrder->planOrderItems()->delete();
        }

        $productIds = $request->get('product_id', []);
        foreach ($productIds as $index => $productId) {
            if(!empty($productId)) {
                $unitId = $request->get('unit_id')[$index];
                $quantity = $request->get('quantity')[$index];
                $realQuantity = $request->get('real_quantity')[$index];

                $actualQuantity = $quantity * $realQuantity;

                $planOrder->planOrderItems()->create([
                    'product_id' => $productId,
                    'unit_id' => $unitId,
                    'quantity' => $quantity,
                    'actual_quantity' => $actualQuantity
                ]);
            }
        }

        return true;
    }
}
