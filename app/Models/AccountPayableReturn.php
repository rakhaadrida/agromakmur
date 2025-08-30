<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPayableReturn extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_payable_id',
        'purchase_return_id',
        'product_id',
        'unit_id',
        'quantity',
        'actual_quantity',
        'price',
        'wages',
        'shipping_cost',
        'total'
    ];

    public function accountPayable() {
        return $this->belongsTo(AccountPayable::class, 'account_payable_id', 'id');
    }

    public function purchaseReturn() {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id', 'id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
