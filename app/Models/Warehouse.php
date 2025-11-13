<?php

namespace App\Models;

use App\Utilities\Traits\FilterByUserBranchRelation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes, FilterByUserBranchRelation;

    protected $fillable = [
        'name',
        'address',
        'type',
    ];

    public function productStocks() {
        return $this->hasMany(ProductStock::class, 'warehouse_id', 'id');
    }

    public function branchWarehouses() {
        return $this->hasMany(BranchWarehouse::class, 'warehouse_id', 'id');
    }

    public function branches() {
        return $this->belongsToMany(Branch::class, 'branch_warehouses')->wherePivot('deleted_at', null);
    }
}
