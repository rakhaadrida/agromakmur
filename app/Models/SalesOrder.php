<?php

namespace App\Models;

use App\Utilities\Constant;
use App\Utilities\Traits\FilterByUserBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use SoftDeletes, FilterByUserBranch;

    protected $fillable = [
        'branch_id',
        'customer_id',
        'marketing_id',
        'number',
        'date',
        'delivery_date',
        'tempo',
        'is_taxable',
        'type',
        'note',
        'subtotal',
        'tax_amount',
        'grand_total',
        'payment_amount',
        'outstanding_amount',
        'is_printed',
        'print_count',
        'status',
        'delivery_status',
        'user_id',
    ];

    public function branch() {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function marketing() {
        return $this->belongsTo(Marketing::class, 'marketing_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function salesOrderItems() {
        return $this->hasMany(SalesOrderItem::class, 'sales_order_id', 'id');
    }

    public function deliveryOrders() {
        return $this->hasMany(DeliveryOrder::class, 'sales_order_id', 'id');
    }

    public function accountReceivable() {
        return $this->hasOne(AccountReceivable::class, 'sales_order_id', 'id');
    }

    public function approvals()
    {
        return $this->morphMany(Approval::class, 'subject');
    }

    public function pendingApprovals()
    {
        return $this->morphMany(Approval::class, 'subject')
            ->where('status', Constant::APPROVAL_STATUS_PENDING)
            ->orderByDesc('id');
    }

    public function pendingApproval()
    {
        return $this->morphOne(Approval::class, 'subject')
            ->where('status', Constant::APPROVAL_STATUS_PENDING)
            ->orderByDesc('id');
    }
}
