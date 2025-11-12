<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPrice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'price_id',
        'base_price',
        'tax_amount',
        'price',
        'is_updated'
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function pricing() {
        return $this->belongsTo(Price::class, 'price_id', 'id');
    }
}
