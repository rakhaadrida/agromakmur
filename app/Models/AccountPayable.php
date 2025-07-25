<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPayable extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_order_id',
        'status',
    ];

    public function purchaseOrder() {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }
}
