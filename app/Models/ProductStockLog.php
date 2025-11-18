<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStockLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subject_type',
        'subject_id',
        'subject_date',
        'type',
        'branch_id',
        'product_id',
        'warehouse_id',
        'customer_id',
        'supplier_id',
        'initial_stock',
        'quantity',
        'final_amount',
        'user_id',
    ];

    public function subject() {
        return $this->morphTo();
    }

    public function branch() {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function warehouse() {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
