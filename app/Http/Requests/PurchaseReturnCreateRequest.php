<?php

namespace App\Http\Requests;

use App\Rules\ValidUniquePurchaseReturnNumber;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseReturnCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'number' => ['required', 'string', new ValidUniquePurchaseReturnNumber(0)],
            'date' => ['required', 'date', 'date_format:d-m-Y'],
            'goods_receipt_id' => ['required', 'exists:goods_receipts,id,deleted_at,NULL'],
            'supplier_id' => ['required', 'exists:suppliers,id,deleted_at,NULL'],
            'received_date' => ['nullable', 'date', 'date_format:d-m-Y'],
            'product_id.*' => ['nullable', 'exists:products,id,deleted_at,NULL'],
            'item_id.*' => ['nullable', 'exists:goods_receipt_items,id,deleted_at,NULL'],
            'unit_id.*' => ['nullable', 'exists:units,id,deleted_at,NULL'],
            'quantity.*' => ['nullable', 'integer'],
            'real_quantity.*' => ['nullable', 'integer'],
            'received_quantity.*' => ['nullable', 'integer'],
            'cut_bill_quantity.*' => ['nullable', 'integer'],
            'is_generated_number' => ['nullable', 'boolean'],
        ];
    }
}
