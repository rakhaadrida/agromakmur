<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'reminder_limit'
    ];

    public function subcategories() {
        return $this->hasMany(Subcategory::class, 'category_id', 'id');
    }
}
