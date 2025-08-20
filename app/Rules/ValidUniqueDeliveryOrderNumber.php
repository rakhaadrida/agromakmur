<?php

namespace App\Rules;

use App\Models\DeliveryOrder;
use App\Models\GoodsReceipt;
use App\Models\SalesOrder;
use Illuminate\Contracts\Validation\Rule;

class ValidUniqueDeliveryOrderNumber implements Rule
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
        $uniqueNumber = DeliveryOrder::query()
            ->where('number', $value)
            ->where('id', '!=', $this->id)
            ->first();

        if($uniqueNumber) return false;

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The delivery order number has already been taken';
    }
}
