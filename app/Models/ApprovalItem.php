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
        'warehouse_id',
        'unit_id',
        'quantity',
        'actual_quantity',
        'price_id',
        'price',
        'wages',
        'shipping_cost',
        'total',
        'discount',
        'discount_amount',
        'final_amount',
        'delivered_quantity',
        'received_quantity',
        'cut_bill_quantity'
    ];

    public function approval() {
        return $this->belongsTo(Approval::class, 'approval_id', 'id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function warehouse() {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function price() {
        return $this->belongsTo(Price::class, 'price_id', 'id');
    }
}
