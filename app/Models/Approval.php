<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Approval extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'subject_type',
        'subject_id',
        'date',
        'type',
        'status',
        'description',
        'subject_date',
        'customer_id',
        'marketing_id',
        'tempo',
        'subtotal',
        'tax_amount',
        'grand_total',
        'user_id',
        'updated_by'
    ];

    public function parent() {
        return $this->belongsTo(Approval::class, 'parent_id', 'id');
    }

    public function activeChild() {
        return $this->hasOne(Approval::class, 'parent_id', 'id')->orderByDesc('id');
    }

    public function subject() {
        return $this->morphTo();
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

    public function updatedBy() {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function approvalItems() {
        return $this->hasMany(ApprovalItem::class, 'approval_id', 'id');
    }
}
