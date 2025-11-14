<?php

namespace App\Models;

use App\Utilities\Traits\FilterItemBySalesOrderBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrderItem extends Model
{
    use SoftDeletes, FilterItemBySalesOrderBranch;

    protected $fillable = [
        'sales_order_id',
        'product_id',
        'warehouse_id',
        'unit_id',
        'quantity',
        'actual_quantity',
        'price_id',
        'price',
        'total',
        'discount',
        'discount_amount',
        'final_amount',
    ];

    public function salesOrder() {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id', 'id');
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
