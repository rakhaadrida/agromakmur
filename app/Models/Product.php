<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'category_id',
        'unit_id',
    ];

    public function category() {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function productConversions() {
        return $this->hasMany(ProductConversion::class, 'product_id', 'id');
    }

    public function productPrices() {
        return $this->hasMany(ProductPrice::class, 'product_id', 'id');
    }

    public function productStocks() {
        return $this->hasMany(ProductStock::class, 'product_id', 'id');
    }

    public function mainPrice() {
        return $this->hasOne(ProductPrice::class, 'product_id', 'id')->oldestOfMany();
    }
}
