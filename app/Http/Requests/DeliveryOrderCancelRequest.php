<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryOrderCancelRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'description' => ['required', 'string'],
            'start_date' => ['nullable', 'date', 'date_format:d-m-Y'],
            'final_date' => ['nullable', 'date', 'date_format:d-m-Y'],
        ];
    }
}
