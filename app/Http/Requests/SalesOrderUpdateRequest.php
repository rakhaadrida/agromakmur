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
            'customer_id' => ['nullable', 'exists:customers,id,deleted_at,NULL'],
            'marketing_id' => ['nullable', 'exists:marketings,id,deleted_at,NULL'],
            'date' => ['nullable', 'date', 'date_format:d-m-Y'],
            'tempo' => ['nullable', 'integer', 'min:0'],
            'description' => ['required', 'string'],
            'start_date' => ['nullable', 'date', 'date_format:d-m-Y'],
            'final_date' => ['nullable', 'date', 'date_format:d-m-Y'],
            'order_number' => ['nullable', 'string'],
            'filter_customer_id' => ['nullable', 'exists:customers,id,deleted_at,NULL'],
            'product_id.*' => ['nullable', 'exists:products,id,deleted_at,NULL'],
            'quantity.*' => ['nullable', 'integer'],
            'real_quantity.*' => ['nullable', 'integer'],
            'unit_id.*' => ['nullable', 'exists:units,id,deleted_at,NULL'],
            'price_id.*' => ['nullable', 'exists:prices,id,deleted_at,NULL'],
            'price.*' => ['nullable', 'integer'],
            'warehouse_ids.*' => ['nullable', 'string'],
            'warehouse_stocks.*' => ['nullable', 'string'],
        ];
    }
}
