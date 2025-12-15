<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountReceivableReturn extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_receivable_id',
        'sales_return_id',
        'product_id',
        'unit_id',
        'quantity',
        'actual_quantity',
        'price_id',
        'price',
        'total',
    ];

    public function accountReceivable() {
        return $this->belongsTo(AccountReceivable::class, 'account_receivable_id', 'id');
    }

    public function salesReturn() {
        return $this->belongsTo(SalesReturn::class, 'sales_return_id', 'id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function price() {
        return $this->belongsTo(Price::class, 'price_id', 'id');
    }
}
