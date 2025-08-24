<?php

namespace App\Utilities\Services;

use App\Models\Approval;
use App\Utilities\Constant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ApprovalService
{
    public static function getBaseQueryIndex($subject) {
        return Approval::query()
            ->select(
                'approvals.*',
                'customers.name AS customer_name',
                'marketings.name AS marketing_name',
                'users.username AS user_name',
                'updated_users.username AS updated_user_name',
            )
            ->leftJoin('customers', 'customers.id', 'approvals.customer_id')
            ->leftJoin('marketings', 'marketings.id', 'approvals.marketing_id')
            ->leftJoin('users', 'users.id', 'approvals.user_id')
            ->leftJoin('users AS updated_users', 'updated_users.id', 'approvals.updated_by')
            ->where('approvals.subject_type', $subject)
            ->whereNull('approvals.parent_id')
            ->whereNull('approvals.deleted_at');
    }

    public static function createData($subject, $subjectItems, $type, $status, $description, $parentId = null) {
        $approval = $subject->approvals()->create([
            'parent_id' => $parentId,
            'date' => Carbon::now(),
            'type' => $type,
            'status' => $status,
            'description' => $description,
            'discount_amount' => $subject->discount_amount ?? 0,
            'user_id' => Auth::user()->id,
        ]);

        if(!$parentId) {
            $subtotal = static::createParentItems($approval, $subjectItems);
        } else {
            $subtotal = static::createChildItems($approval, $subjectItems);
        }

        $totalAfterDiscount = $subtotal - $approval->discount_amount;
        $taxAmount = round($totalAfterDiscount * (10 / 100));
        $grandTotal = (int) $totalAfterDiscount + $taxAmount;

        $approval->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'grand_total' => $grandTotal
        ]);

        return $approval;
    }

    public static function createDataSalesOrder($subject, $subjectItems, $type, $status, $description, $parentId, $subjectData) {
        $date = $subjectData['date'];
        $date = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

        $approval = $subject->approvals()->create([
            'parent_id' => $parentId,
            'date' => Carbon::now(),
            'type' => $type,
            'status' => $status,
            'description' => $description,
            'subject_date' => $date,
            'customer_id' => $subjectData['customer_id'],
            'marketing_id' => $subjectData['marketing_id'],
            'tempo' => $subjectData['tempo'],
            'discount_amount' => $subjectData['invoice_discount'] ?? 0,
            'user_id' => Auth::user()->id,
        ]);

        $subtotal = static::createChildItemSalesOrders($approval, $subjectItems);

        $totalAfterDiscount = $subtotal - $approval->discount_amount;
        $taxAmount = round($totalAfterDiscount * (10 / 100));
        $grandTotal = (int) $totalAfterDiscount + $taxAmount;

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
            $totalExpenses = $item->wages + $item->shipping_cost;
            $total = ($item->quantity * $item->price) + $totalExpenses;
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
                'wages' => $item->wages ?? 0,
                'shipping_cost' => $item->shipping_cost ?? 0,
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
                $price = $childItems['price'][$index] ?? 0;
                $wages = $childItems['wages'][$index] ?? 0;
                $shippingCost = $childItems['shipping_cost'][$index] ?? 0;
                $discount = $childItems['discount'][$index] ?? 0;
                $discountAmount = $childItems['discount_amount'][$index] ?? 0;

                $actualQuantity = $quantity * $realQuantity;
                $totalExpenses = $wages + $shippingCost;
                $total = ($quantity * $price) + $totalExpenses;
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
                    'wages' => $wages,
                    'shipping_cost' => $shippingCost,
                    'total' => $total,
                    'discount' => $discount,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $finalAmount,
                ]);
            }
        }

        return $subtotal;
    }

    protected static function createChildItemSalesOrders($approval, $childItems) {
        $subtotal = 0;
        foreach ($childItems as $item) {
            $totalDiscount = $item['discount_product'];
            $warehouseCount = count($item['warehouse_ids']);

            foreach ($item['warehouse_ids'] as $key => $warehouseId) {
                $quantity = $item['warehouse_stocks'][$key] ?? 0;
                $actualQuantity = $quantity * $item['real_quantity'];
                $total = $quantity * $item['price'];

                $discountValue = round($item['discount_product'] / $warehouseCount);
                if ($discountValue < $totalDiscount) {
                    $totalDiscount -= $discountValue;
                } else {
                    $discountValue = $totalDiscount;
                    $totalDiscount = 0;
                }

                $finalAmount = $total - $discountValue;
                $subtotal += $finalAmount;

                $approval->approvalItems()->create([
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $warehouseId,
                    'unit_id' => $item['unit_id'],
                    'quantity' => $quantity,
                    'actual_quantity' => $actualQuantity,
                    'price_id' => $item['price_id'],
                    'price' => $item['price'],
                    'total' => $total,
                    'discount' => $item['discount'],
                    'discount_amount' => $discountValue,
                    'final_amount' => $finalAmount
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
