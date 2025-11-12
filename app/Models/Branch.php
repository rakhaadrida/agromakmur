<?php

namespace App\Models;

use App\Utilities\Traits\FilterByUserBranchTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes, FilterByUserBranchTable;

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
