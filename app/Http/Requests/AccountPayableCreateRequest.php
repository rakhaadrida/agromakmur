<?php

namespace App\Http\Requests;

use App\Rules\ValidUniqueDeliveryOrderNumber;
use Illuminate\Foundation\Http\FormRequest;

class AccountPayableCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'start_date' => ['nullable', 'date', 'date_format:d-m-Y'],
            'final_date' => ['nullable', 'date', 'date_format:d-m-Y'],
            'status' => ['nullable', 'string'],
            'payable_id' => ['required', 'exists:account_payables,id,deleted_at,NULL'],
            'payment_date.*' => ['nullable', 'date', 'date_format:d-m-Y'],
            'payment_amount.*' => ['nullable', 'integer'],
        ];
    }
}
