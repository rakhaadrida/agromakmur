<?php

namespace App\Models;

use App\Utilities\Constant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReturn extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'sales_order_id',
        'number',
        'date',
        'delivery_date',
        'is_printed',
        'print_count',
        'status',
        'delivery_status',
        'user_id',
    ];

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function salesOrder() {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function salesReturnItems() {
        return $this->hasMany(SalesReturnItem::class, 'sales_return_id', 'id');
    }

    public function accountReceivableReturns() {
        return $this->hasMany(AccountReceivableReturn::class, 'sales_return_id', 'id');
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
