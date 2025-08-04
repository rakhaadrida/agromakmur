<?php

namespace App\Models;

use App\Utilities\Constant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceipt extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'warehouse_id',
        'number',
        'date',
        'tempo',
        'subtotal',
        'tax_amount',
        'grand_total',
        'is_printed',
        'status',
        'user_id',
    ];

    public function supplier() {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function warehouse() {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function goodsReceiptItems() {
        return $this->hasMany(GoodsReceiptItem::class, 'goods_receipt_id', 'id');
    }

    public function approvals()
    {
        return $this->morphMany(Approval::class, 'subject');
    }

    public function pendingApproval()
    {
        return $this->morphOne(Approval::class, 'subject')->where('status', Constant::APPROVAL_STATUS_PENDING);
    }
}
