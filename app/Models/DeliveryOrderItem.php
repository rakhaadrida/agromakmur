<?php

namespace App\Models;

use App\Utilities\Traits\FilterDeliveryItemByDeliveryOrderBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryOrderItem extends Model
{
    use SoftDeletes, FilterDeliveryItemByDeliveryOrderBranch;

    protected $fillable = [
        'delivery_order_id',
        'sales_order_item_id',
        'product_id',
        'unit_id',
        'quantity',
        'actual_quantity',
    ];

    public function deliveryOrder() {
        return $this->belongsTo(DeliveryOrder::class, 'delivery_order_id', 'id');
    }

    public function salesOrderItem() {
        return $this->belongsTo(SalesOrderItem::class, 'sales_order_item_id', 'id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
