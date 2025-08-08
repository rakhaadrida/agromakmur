<?php

namespace App\Utilities\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ApprovalService
{
    public static function createData($subject, $subjectItems, $type, $status, $description, $parentId = null) {
        $approval = $subject->approvals()->create([
            'parent_id' => $parentId,
            'date' => Carbon::now(),
            'type' => $type,
            'status' => $status,
            'description' => $description,
            'user_id' => Auth::user()->id,
        ]);

        if(!$parentId) {
            $subtotal = static::createParentItems($approval, $subjectItems);
        } else {
            $subtotal = static::createChildItems($approval, $subjectItems);
        }

        $taxAmount = $subtotal * (10 / 100);
        $grandTotal = $subtotal + $taxAmount;

        $approval->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'grand_total' => $grandTotal
        ]);

        return $approval;
    }

    protected static function createParentItems($approval, $parentItems) {
        $subtotal = 0;
        foreach($parentItems as $item) {
            $total = $item->quantity * $item->price;
            $finalAmount = $total - $item->discount_amount ?? 0;
            $subtotal += $finalAmount;

            $approval->approvalItems()->create([
                'product_id' => $item->product_id,
                'unit_id' => $item->unit_id,
                'warehouse_id' => $item->warehouse_id ?? null,
                'quantity' => $item->quantity,
                'actual_quantity' => $item->actual_quantity,
                'price_id' => $item->price_id ?? null,
                'price' => $item->price ?? 0,
                'total' => $total ?? 0,
                'discount' => $item->discount ?? 0,
                'discount_amount' => $item->discount_amount ?? 0,
                'final_amount' => $finalAmount ?? 0,
            ]);
        }

        return $subtotal;
    }

    protected static function createChildItems($approval, $childItems) {
        $subtotal = 0;
        $productIds = $childItems['product_id'] ?? [];

        foreach ($productIds as $index => $productId) {
            if(!empty($productId)) {
                $warehouseId = $childItems['warehouse_id'][$index] ?? null;
                $unitId = $childItems['unit_id'][$index];
                $quantity = $childItems['quantity'][$index];
                $realQuantity = $childItems['real_quantity'][$index];
                $priceId = $childItems['price_id'][$index] ?? null;
                $price = $childItems['price'][$index];
                $discount = $childItems['discount'][$index] ?? 0;
                $discountAmount = $childItems['discount_amount'][$index] ?? 0;

                $actualQuantity = $quantity * $realQuantity;
                $total = $quantity * $price;
                $finalAmount = $total - $discountAmount;
                $subtotal += $finalAmount;

                $approval->approvalItems()->create([
                    'product_id' => $productId,
                    'warehouse_id' => $warehouseId,
                    'unit_id' => $unitId,
                    'quantity' => $quantity,
                    'actual_quantity' => $actualQuantity,
                    'price_id' => $priceId,
                    'price' => $price,
                    'total' => $total,
                    'discount' => $discount,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $finalAmount,
                ]);
            }
        }

        return $subtotal;
    }

    public static function deleteData($approvals) {
        foreach($approvals as $approval) {
            $approval->approvalItems()->delete();
            $approval->delete();
        }
    }
}
