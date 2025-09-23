<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Price extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'type',
    ];

    public function productPrices() {
        return $this->hasMany(ProductPrice::class, 'price_id', 'id');
    }
}
