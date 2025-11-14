<?php

namespace App\Models;

use App\Utilities\Constant;
use App\Utilities\Traits\FilterItemByGoodsReceiptBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReturn extends Model
{
    use SoftDeletes, FilterItemByGoodsReceiptBranch;

    protected $fillable = [
        'supplier_id',
        'goods_receipt_id',
        'number',
        'date',
        'received_date',
        'is_printed',
        'print_count',
        'status',
        'receipt_status',
        'user_id',
    ];

    public function supplier() {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function goodsReceipt() {
        return $this->belongsTo(GoodsReceipt::class, 'goods_receipt_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function purchaseReturnItems() {
        return $this->hasMany(PurchaseReturnItem::class, 'purchase_return_id', 'id');
    }

    public function accountPayableReturns() {
        return $this->hasMany(AccountPayableReturn::class, 'purchase_return_id', 'id');
    }

    public function approvals()
    {
        return $this->morphMany(Approval::class, 'subject');
    }

    public function pendingApproval()
    {
        return $this->morphOne(Approval::class, 'subject')
            ->where('status', Constant::APPROVAL_STATUS_PENDING)
            ->orderByDesc('id');
    }
}
