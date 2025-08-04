<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Approval extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subject_type',
        'subject_id',
        'date',
        'type',
        'status',
        'description',
        'user_id',
    ];

    public function subject() {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function approvalItems() {
        return $this->hasMany(ApprovalItem::class, 'approval_id', 'id');
    }
}
