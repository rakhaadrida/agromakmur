<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReturnItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sales_return_id',
        'sales_order_item_id',
        'product_id',
        'unit_id',
        'order_quantity',
        'quantity',
        'actual_quantity',
        'delivered_quantity',
        'cut_bill_quantity',
    ];

    public function salesReturn() {
        return $this->belongsTo(SalesReturn::class, 'sales_return_id', 'id');
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
