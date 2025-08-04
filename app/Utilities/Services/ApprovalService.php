<?php

namespace App\Utilities\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ApprovalService
{
    public static function createData($subject, $subjectItems, $type, $status, $description) {
        $approval = $subject->approvals()->create([
            'date' => Carbon::now(),
            'type' => $type,
            'status' => $status,
            'description' => $description,
            'user_id' => Auth::user()->id,
        ]);

        foreach($subjectItems as $item) {
            $approval->approvalItems()->create([
                'product_id' => $item->product_id,
                'unit_id' => $item->unit_id,
                'quantity' => $item->quantity,
                'actual_quantity' => $item->actual_quantity,
                'price' => $item->price ?? 0,
                'discount' => $item->discount ?? 0,
                'discount_amount' => $item->discount_amount ?? 0,
                'total' => $item->total ?? 0,
            ]);
        }

        return true;
    }
}
