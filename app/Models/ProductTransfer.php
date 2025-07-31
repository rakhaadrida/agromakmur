<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTransfer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number',
        'date',
        'is_printed',
        'status',
        'user_id',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function productTransferItems() {
        return $this->hasMany(ProductTransferItem::class, 'product_transfer_id', 'id');
    }
}
