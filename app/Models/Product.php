<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
        'subcategory_id',
        'unit_id',
    ];

    public function category() {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function subcategory() {
        return $this->belongsTo(Subcategory::class, 'subcategory_id', 'id');
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
}
