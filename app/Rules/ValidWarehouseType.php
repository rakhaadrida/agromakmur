<?php

namespace App\Rules;

use App\Utilities\Constant;
use Illuminate\Contracts\Validation\Rule;

class ValidWarehouseType implements Rule
{

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        return in_array($value, Constant::WAREHOUSE_TYPES);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid Warehouse Type';
    }
}
