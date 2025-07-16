<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStock extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'stock',
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function warehouse() {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }
}
