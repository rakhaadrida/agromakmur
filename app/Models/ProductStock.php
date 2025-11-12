<?php

namespace App\Models;

use App\Utilities\Traits\FilterStockByBranchWarehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStock extends Model
{
    use SoftDeletes, FilterStockByBranchWarehouse;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'stock',
        'is_updated'
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function warehouse() {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }
}
