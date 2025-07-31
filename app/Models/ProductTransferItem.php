<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTransferItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_transfer_id',
        'product_id',
        'source_warehouse_id',
        'destination_warehouse_id',
        'quantity',
        'actual_quantity',
        'unit_id',
    ];

    public function productTransfer() {
        return $this->belongsTo(ProductTransfer::class, 'product_transfer_id', 'id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function sourceWarehouse() {
        return $this->belongsTo(Warehouse::class, 'source_warehouse_id', 'id');
    }

    public function destinationWarehouse() {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
