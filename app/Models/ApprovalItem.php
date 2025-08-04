<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprovalItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'approval_id',
        'product_id',
        'unit_id',
        'quantity',
        'actual_quantity',
        'price',
        'discount',
        'discount_amount',
        'total',
    ];

    public function approval() {
        return $this->belongsTo(Approval::class, 'approval_id', 'id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
