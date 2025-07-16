<?php

namespace App\Http\Requests;

use App\Rules\ValidProductSubcategory;
use Illuminate\Foundation\Http\FormRequest;

class ProductStockRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'warehouse_id.*' => ['required', 'exists:warehouses,id,deleted_at,NULL'],
            'stock.*' => ['required', 'numeric', 'min:0'],
        ];
    }
}
