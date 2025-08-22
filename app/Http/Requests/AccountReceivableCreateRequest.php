<?php

namespace App\Http\Requests;

use App\Rules\ValidUniqueDeliveryOrderNumber;
use Illuminate\Foundation\Http\FormRequest;

class AccountReceivableCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'receivable_id' => ['required', 'exists:account_receivables,id,deleted_at,NULL'],
            'payment_date.*' => ['nullable', 'date', 'date_format:d-m-Y'],
            'payment_amount.*' => ['nullable', 'integer'],
        ];
    }
}
