<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'phone_number',
    ];

    public function userBranches() {
        return $this->hasMany(UserBranch::class, 'branch_id', 'id');
    }

    public function branchWarehouses() {
        return $this->hasMany(BranchWarehouse::class, 'branch_id', 'id');
    }
}
