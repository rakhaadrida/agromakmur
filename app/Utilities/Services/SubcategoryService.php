<?php

namespace App\Utilities\Services;

use App\Models\Subcategory;

class SubcategoryService
{
    public static function restoreSubcategoryByCategoryId($categoryId) {
        $subcategories = Subcategory::onlyTrashed()
            ->where('is_destroy', 0)
            ->whereHas('category', function($query) {
                $query->where('is_destroy', 0);
            });

        if($categoryId) {
            $subcategories->where('category_id', $categoryId);
        }

        $subcategories->restore();

        return true;
    }
}
