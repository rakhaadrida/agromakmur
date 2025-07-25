<?php

namespace App\Http\Requests;

use App\Rules\ValidProductSubcategory;
use App\Rules\ValidUniquePurchaseOrderNumber;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'number' => ['required', 'string', 'max:255', new ValidUniquePurchaseOrderNumber(0)],
            'warehouse_id' => ['required', 'exists:warehouses,id,deleted_at,NULL'],
            'supplier_id' => ['required', 'exists:suppliers,id,deleted_at,NULL'],
            'date' => ['required', 'date', 'date_format:d-m-Y'],
            'tempo' => ['nullable', 'integer', 'min:0'],
            'product_id.*' => ['nullable', 'exists:products,id,deleted_at,NULL'],
            'quantity.*' => ['nullable', 'integer'],
            'unit_id.*' => ['nullable', 'exists:units,id,deleted_at,NULL'],
            'price.*' => ['nullable', 'integer'],
            'real_quantity.*' => ['nullable', 'integer'],
        ];
    }
}
