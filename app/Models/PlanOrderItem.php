<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanOrderItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'plan_order_id',
        'product_id',
        'unit_id',
        'quantity',
        'actual_quantity',
        'price',
        'total',
    ];

    public function planOrder() {
        return $this->belongsTo(PlanOrder::class, 'plan_order_id', 'id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
