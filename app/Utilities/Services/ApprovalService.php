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
            $subtotal += $total;

            $approval->approvalItems()->create([
                'product_id' => $item->product_id,
                'unit_id' => $item->unit_id,
                'quantity' => $item->quantity,
                'actual_quantity' => $item->actual_quantity,
                'price' => $item->price ?? 0,
                'discount' => $item->discount ?? 0,
                'discount_amount' => $item->discount_amount ?? 0,
                'total' => $total ?? 0,
            ]);
        }

        return $subtotal;
    }

    protected static function createChildItems($approval, $childItems) {
        $subtotal = 0;
        $productIds = $childItems['product_id'] ?? [];

        foreach ($productIds as $index => $productId) {
            if(!empty($productId)) {
                $unitId = $childItems['unit_id'][$index];
                $quantity = $childItems['quantity'][$index];
                $realQuantity = $childItems['real_quantity'][$index];
                $price = $childItems['price'][$index];

                $actualQuantity = $quantity * $realQuantity;
                $total = $quantity * $price;
                $subtotal += $total;

                $approval->approvalItems()->create([
                    'product_id' => $productId,
                    'unit_id' => $unitId,
                    'quantity' => $quantity,
                    'actual_quantity' => $actualQuantity,
                    'price' => $price,
                    'discount' => $item->discount ?? 0,
                    'discount_amount' => $item->discount_amount ?? 0,
                    'total' => $total,
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
