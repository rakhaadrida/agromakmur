<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'contact_number' => ['nullable', 'string'],
            'tax_number' => ['nullable', 'string', 'min:15', 'max:16'],
            'credit_limit' => ['required', 'numeric', 'min:0'],
            'tempo' => ['required', 'numeric', 'min:0'],
            'marketing_id' => ['nullable', 'exists:marketings,id,deleted_at,NULL'],
        ];
    }
}
