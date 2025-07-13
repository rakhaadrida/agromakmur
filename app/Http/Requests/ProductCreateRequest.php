<?php

namespace App\Http\Requests;

use App\Rules\ValidProductSubcategory;
use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id,deleted_at,NULL'],
            'subcategory_id' => ['nullable', new ValidProductSubcategory(
                $this->input('category_id'),
                $this->input('subcategory_id')
            )],
            'unit_id' => ['nullable', 'exists:units,id,deleted_at,NULL'],
            'conversion' => ['nullable'],
            'unit_conversion_id' => ['required_if_accepted:conversion', 'exists:units,id,deleted_at,NULL', 'different:unit_id'],
            'quantity' => ['required_if_accepted:conversion', 'numeric', 'min:1'],
            'base_price.*' => ['required', 'numeric', 'min:0'],
            'tax_amount.*' => ['required', 'numeric', 'min:0'],
            'price.*' => ['required', 'numeric', 'min:0'],
            'price_id.*' => ['nullable', 'exists:prices,id,deleted_at,NULL'],
        ];
    }
}
