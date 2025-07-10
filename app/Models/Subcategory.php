<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'reminder_limit',
        'category_id'
    ];

    public function category() {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
