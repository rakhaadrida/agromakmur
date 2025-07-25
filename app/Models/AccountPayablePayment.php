<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPayablePayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_payable_id',
        'date',
        'amount',
    ];

    public function accountPayable() {
        return $this->belongsTo(AccountPayable::class, 'account_payable_id', 'id');
    }
}
