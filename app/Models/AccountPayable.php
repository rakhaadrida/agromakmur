<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPayable extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'goods_receipt_id',
        'status',
    ];

    public function goodsReceipt() {
        return $this->belongsTo(GoodsReceipt::class, 'goods_receipt_id', 'id');
    }
}
