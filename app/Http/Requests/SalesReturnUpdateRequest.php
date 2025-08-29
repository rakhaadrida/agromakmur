<?php

namespace App\Http\Requests;

use App\Rules\ValidUniqueSalesReturnNumber;
use Illuminate\Foundation\Http\FormRequest;

class SalesReturnUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'delivery_date' => ['nullable', 'date', 'date_format:d-m-Y'],
            'product_id.*' => ['nullable', 'exists:products,id,deleted_at,NULL'],
            'item_id.*' => ['nullable', 'exists:sales_order_items,id,deleted_at,NULL'],
            'unit_id.*' => ['nullable', 'exists:units,id,deleted_at,NULL'],
            'quantity.*' => ['nullable', 'integer'],
            'real_quantity.*' => ['nullable', 'integer'],
            'delivered_quantity.*' => ['nullable', 'integer'],
            'cut_bill_quantity.*' => ['nullable', 'integer'],
        ];
    }
}
