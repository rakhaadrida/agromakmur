<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesOrderUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'description' => ['required', 'string'],
            'product_id.*' => ['nullable', 'exists:products,id,deleted_at,NULL'],
            'quantity.*' => ['nullable', 'integer'],
            'unit_id.*' => ['nullable', 'exists:units,id,deleted_at,NULL'],
            'price.*' => ['nullable', 'integer'],
            'real_quantity.*' => ['nullable', 'integer'],
        ];
    }
}
