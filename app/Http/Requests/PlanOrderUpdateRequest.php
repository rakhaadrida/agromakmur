<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanOrderUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'description' => ['required', 'string'],
            'start_date' => ['nullable', 'date', 'date_format:d-m-Y'],
            'final_date' => ['nullable', 'date', 'date_format:d-m-Y'],
            'order_number' => ['nullable', 'string'],
            'supplier_id' => ['nullable', 'exists:suppliers,id,deleted_at,NULL'],
            'product_id.*' => ['nullable', 'exists:products,id,deleted_at,NULL'],
            'quantity.*' => ['nullable', 'integer'],
            'unit_id.*' => ['nullable', 'exists:units,id,deleted_at,NULL'],
            'real_quantity.*' => ['nullable', 'integer'],
        ];
    }
}
