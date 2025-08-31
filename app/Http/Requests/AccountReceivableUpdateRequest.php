<?php

namespace App\Http\Requests;

use App\Rules\ValidUniqueDeliveryOrderNumber;
use Illuminate\Foundation\Http\FormRequest;

class AccountReceivableUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sales_return_id.*' => ['required', 'exists:sales_returns,id,deleted_at,NULL'],
            'product_id.*' => ['required', 'exists:products,id,deleted_at,NULL'],
            'quantity.*' => ['required', 'integer'],
            'real_quantity.*' => ['required', 'integer'],
            'unit_id.*' => ['required', 'exists:units,id,deleted_at,NULL'],
            'price_id.*' => ['required', 'exists:prices,id,deleted_at,NULL'],
            'price.*' => ['required', 'integer'],
            'discount.*' => ['nullable', 'string'],
            'discount_product.*' => ['nullable', 'integer'],
        ];
    }
}
