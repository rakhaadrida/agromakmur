<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountReceivablePayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_receivable_id',
        'date',
        'amount',
    ];

    public function accountReceivable() {
        return $this->belongsTo(AccountReceivable::class, 'account_receivable_id', 'id');
    }
}
