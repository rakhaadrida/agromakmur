<?php

namespace App\Models;

use App\Utilities\Constant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'number',
        'date',
        'subtotal',
        'tax_amount',
        'grand_total',
        'is_printed',
        'user_id',
    ];

    public function supplier() {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function planOrderItems() {
        return $this->hasMany(PlanOrderItem::class, 'plan_order_id', 'id');
    }
}
