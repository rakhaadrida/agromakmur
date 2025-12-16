<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoodsReceiptUpdateRequest extends FormRequest
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
            'product_id.*' => ['nullable', 'exists:products,id,deleted_at,NULL'],
            'quantity.*' => ['nullable', 'integer'],
            'unit_id.*' => ['nullable', 'exists:units,id,deleted_at,NULL'],
            'price.*' => ['nullable', 'integer'],
            'wages.*' => ['nullable', 'integer'],
            'shipping_cost.*' => ['nullable', 'integer'],
            'real_quantity.*' => ['nullable', 'integer'],
        ];
    }
}
