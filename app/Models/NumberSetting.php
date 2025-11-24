<?php

namespace App\Models;

use App\Utilities\Traits\FilterByUserBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NumberSetting extends Model
{
    use SoftDeletes, FilterByUserBranch;

    protected $fillable = [
        'key',
        'branch_id',
        'year',
        'month',
        'last_number',
    ];

    public function branch() {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
