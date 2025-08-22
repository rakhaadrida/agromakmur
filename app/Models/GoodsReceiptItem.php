<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceiptItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'goods_receipt_id',
        'product_id',
        'unit_id',
        'quantity',
        'actual_quantity',
        'price',
        'wages',
        'shipping_cost',
        'total',
    ];

    public function goodsReceipt() {
        return $this->belongsTo(GoodsReceipt::class, 'goods_receipt_id', 'id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
