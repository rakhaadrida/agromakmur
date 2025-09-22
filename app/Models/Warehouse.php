<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'type',
    ];

    public function productStocks() {
        return $this->hasMany(ProductStock::class, 'warehouse_id', 'id');
    }
}
