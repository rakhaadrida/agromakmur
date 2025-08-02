<?php

namespace App\Http\Requests;

use App\Rules\ValidProductSubcategory;
use App\Rules\ValidUniqueGoodsReceiptNumber;
use App\Rules\ValidUniqueProductTransferNumber;
use Illuminate\Foundation\Http\FormRequest;

class ProductTransferCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'number' => ['required', 'string', 'max:255', new ValidUniqueProductTransferNumber(0)],
            'date' => ['required', 'date', 'date_format:d-m-Y'],
            'product_id.*' => ['nullable', 'exists:products,id,deleted_at,NULL'],
            'unit_id.*' => ['nullable', 'exists:units,id,deleted_at,NULL'],
            'source_warehouse_id.*' => ['nullable', 'exists:warehouses,id,deleted_at,NULL'],
            'destination_warehouse_id.*' => ['nullable', 'exists:warehouses,id,deleted_at,NULL', 'different:source_warehouse_id'],
            'quantity.*' => ['nullable', 'integer'],
            'real_quantity.*' => ['nullable', 'integer'],
            'is_print' => ['nullable', 'boolean'],
        ];
    }
}
