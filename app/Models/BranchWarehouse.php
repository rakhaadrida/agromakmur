<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchWarehouse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'warehouse_id',
        'is_updated'
    ];

    public function branch() {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function warehouse() {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }
}
