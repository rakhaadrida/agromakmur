<?php

namespace App\Http\Requests;

use App\Rules\ValidSalesOrderCustomer;
use App\Rules\ValidUniqueSalesOrderNumber;
use Illuminate\Foundation\Http\FormRequest;

class SalesOrderCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'number' => ['required', 'string', 'max:255', new ValidUniqueSalesOrderNumber(0)],
            'branch_id' => ['required', 'exists:branches,id,deleted_at,NULL'],
            'customer_id' => ['required_without:new_customer', new ValidSalesOrderCustomer()],
            'marketing_id' => ['required', 'exists:marketings,id,deleted_at,NULL'],
            'date' => ['required', 'date', 'date_format:d-m-Y'],
            'delivery_date' => ['required', 'date', 'date_format:d-m-Y'],
            'tempo' => ['nullable', 'integer', 'min:0'],
            'is_taxable' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string'],
            'new_customer' => ['nullable', 'string'],
            'payment_amount' => ['nullable', 'integer', 'min:0'],
            'product_id.*' => ['nullable', 'exists:products,id,deleted_at,NULL'],
            'quantity.*' => ['nullable', 'integer'],
            'real_quantity.*' => ['nullable', 'integer'],
            'unit_id.*' => ['nullable', 'exists:units,id,deleted_at,NULL'],
            'price_id.*' => ['nullable', 'exists:prices,id,deleted_at,NULL'],
            'price.*' => ['nullable', 'integer'],
            'warehouse_ids.*' => ['nullable', 'string'],
            'warehouse_stocks.*' => ['nullable', 'string'],
            'is_print' => ['nullable', 'boolean'],
            'is_print_bill' => ['nullable', 'boolean'],
            'is_generated_number' => ['nullable', 'boolean'],
        ];
    }
}
