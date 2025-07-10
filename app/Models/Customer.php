<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'contact_number',
        'tax_number',
        'credit_limit',
        'tempo',
        'marketing_id'
    ];

    public function marketing() {
        return $this->belongsTo(Marketing::class, 'marketing_id', 'id');
    }
}
