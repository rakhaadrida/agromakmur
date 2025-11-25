<?php

namespace App\Http\Requests;

use App\Rules\ValidUniqueSalesReturnNumber;
use Illuminate\Foundation\Http\FormRequest;

class SalesReturnCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'number' => ['required', 'string', new ValidUniqueSalesReturnNumber(0)],
            'date' => ['required', 'date', 'date_format:d-m-Y'],
            'sales_order_id' => ['required', 'exists:sales_orders,id,deleted_at,NULL'],
            'customer_id' => ['required', 'exists:customers,id,deleted_at,NULL'],
            'delivery_date' => ['nullable', 'date', 'date_format:d-m-Y'],
            'product_id.*' => ['nullable', 'exists:products,id,deleted_at,NULL'],
            'item_id.*' => ['nullable', 'exists:sales_order_items,id,deleted_at,NULL'],
            'unit_id.*' => ['nullable', 'exists:units,id,deleted_at,NULL'],
            'quantity.*' => ['nullable', 'integer'],
            'real_quantity.*' => ['nullable', 'integer'],
            'delivered_quantity.*' => ['nullable', 'integer'],
            'cut_bill_quantity.*' => ['nullable', 'integer'],
            'is_generated_number' => ['nullable', 'boolean'],
        ];
    }
}
