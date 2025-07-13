<?php

namespace App\Rules;

use App\Models\Subcategory;
use Illuminate\Contracts\Validation\Rule;

class ValidProductSubcategory implements Rule
{
    protected $categoryId;
    protected $subcategoryId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($categoryId, $subcategoryId)
    {
        $this->categoryId = $categoryId;
        $this->subcategoryId = $subcategoryId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $subcategory = Subcategory::query()
            ->where('id', $this->subcategoryId)
            ->where('category_id', $this->categoryId)
            ->first();

        if(!$subcategory) return false;

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid subcategory for the selected category.';
    }
}
