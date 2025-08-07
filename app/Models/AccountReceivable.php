<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountReceivable extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sales_order_id',
        'status',
    ];

    public function salesOrder() {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id', 'id');
    }
}
