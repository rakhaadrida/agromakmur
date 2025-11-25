<?php

namespace App\Http\Requests;

use App\Rules\ValidUniqueDeliveryOrderNumber;
use Illuminate\Foundation\Http\FormRequest;

class DeliveryOrderCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'number' => ['required', 'string', new ValidUniqueDeliveryOrderNumber(0)],
            'date' => ['required', 'date', 'date_format:d-m-Y'],
            'sales_order_id' => ['nullable', 'exists:sales_orders,id,deleted_at,NULL'],
            'branch_id' => ['nullable', 'exists:branches,id,deleted_at,NULL'],
            'customer_id' => ['nullable', 'exists:customers,id,deleted_at,NULL'],
            'address' => ['required', 'string'],
            'product_id.*' => ['nullable', 'exists:products,id,deleted_at,NULL'],
            'unit_id.*' => ['nullable', 'exists:units,id,deleted_at,NULL'],
            'order_quantity.*' => ['nullable', 'integer'],
            'quantity.*' => ['nullable', 'integer'],
            'real_quantity.*' => ['nullable', 'integer'],
            'is_generated_number' => ['nullable', 'boolean'],
        ];
    }
}
