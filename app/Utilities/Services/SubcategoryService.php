<?php

namespace App\Utilities\Services;

use App\Models\Subcategory;

class SubcategoryService
{
    public static function restoreSubcategoryByCategoryId($categoryId) {
        $subcategories = Subcategory::onlyTrashed()
            ->where('category_id', $categoryId);

        $subcategories->restore();

        return true;
    }
}
