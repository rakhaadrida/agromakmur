<?php

namespace App\Http\Requests;

use App\Rules\ValidUniquePlanOrderNumber;
use Illuminate\Foundation\Http\FormRequest;

class PlanOrderCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'number' => ['required', 'string', 'max:255', new ValidUniquePlanOrderNumber(0)],
            'branch_id' => ['required', 'exists:branches,id,deleted_at,NULL'],
            'supplier_id' => ['required', 'exists:suppliers,id,deleted_at,NULL'],
            'date' => ['required', 'date', 'date_format:d-m-Y'],
            'product_id.*' => ['nullable', 'exists:products,id,deleted_at,NULL'],
            'quantity.*' => ['nullable', 'integer'],
            'unit_id.*' => ['nullable', 'exists:units,id,deleted_at,NULL'],
            'price.*' => ['nullable', 'integer'],
            'real_quantity.*' => ['nullable', 'integer'],
            'is_print' => ['nullable', 'boolean'],
        ];
    }
}
