<?php

namespace App\Http\Requests;

use App\Rules\ValidUniqueDeliveryOrderNumber;
use Illuminate\Foundation\Http\FormRequest;

class AccountPayableUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'purchase_return_id.*' => ['required', 'exists:purchase_returns,id,deleted_at,NULL'],
            'product_id.*' => ['required', 'exists:products,id,deleted_at,NULL'],
            'quantity.*' => ['required', 'integer'],
            'real_quantity.*' => ['required', 'integer'],
            'unit_id.*' => ['required', 'exists:units,id,deleted_at,NULL'],
            'price.*' => ['required', 'integer'],
            'wages.*' => ['nullable', 'string'],
            'shipping_cost.*' => ['nullable', 'integer'],
        ];
    }
}
