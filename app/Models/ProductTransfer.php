<?php

namespace App\Models;

use App\Utilities\Constant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTransfer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number',
        'date',
        'is_printed',
        'status',
        'user_id',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function productTransferItems() {
        return $this->hasMany(ProductTransferItem::class, 'product_transfer_id', 'id');
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
