<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryOrderUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => ['nullable', 'date', 'date_format:d-m-Y'],
            'description' => ['required', 'string'],
            'product_id.*' => ['nullable', 'exists:products,id,deleted_at,NULL'],
            'unit_id.*' => ['nullable', 'exists:units,id,deleted_at,NULL'],
            'quantity.*' => ['nullable', 'integer'],
            'real_quantity.*' => ['nullable', 'integer'],
        ];
    }
}
