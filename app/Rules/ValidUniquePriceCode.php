<?php

namespace App\Rules;

use App\Models\Price;
use Illuminate\Contracts\Validation\Rule;

class ValidUniquePriceCode implements Rule
{
    protected $id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
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
        $uniqueCode = Price::query()
            ->where('code', $value)
            ->where('id', '!=', $this->id)
            ->first();

        if($uniqueCode) return false;

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The code has already been taken';
    }
}
