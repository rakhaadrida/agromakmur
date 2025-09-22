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
            'category_id' => ['required', 'exists:categories,id,deleted_at,NULL'],
            'subcategory_id' => ['nullable', new ValidProductSubcategory(
                $this->input('category_id'),
                $this->input('subcategory_id')
            )],
            'unit_id' => ['nullable', 'exists:units,id,deleted_at,NULL'],
            'has_conversion' => ['sometimes'],
            'unit_conversion_id' => ['exclude_without:has_conversion', 'exists:units,id,deleted_at,NULL', 'different:unit_id'],
            'quantity' => ['exclude_without:has_conversion', 'numeric', 'min:1'],
            'price_id.*' => ['nullable', 'exists:prices,id,deleted_at,NULL'],
            'base_price.*' => ['required_with:price_id', 'numeric', 'min:0'],
            'tax_amount.*' => ['required_with:price_id', 'numeric', 'min:0'],
            'price.*' => ['required_with:price_id', 'numeric', 'min:0'],
        ];
    }
}
