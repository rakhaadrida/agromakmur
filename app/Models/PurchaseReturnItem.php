<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReturnItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_return_id',
        'goods_receipt_item_id',
        'product_id',
        'unit_id',
        'quantity',
        'actual_quantity',
        'received_quantity',
        'cut_bill_quantity',
    ];

    public function purchaseReturn() {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id', 'id');
    }

    public function goodsReceiptItem() {
        return $this->belongsTo(GoodsReceiptItem::class, 'goods_receipt_item_id', 'id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
