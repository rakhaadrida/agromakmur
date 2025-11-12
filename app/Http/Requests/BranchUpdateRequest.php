<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchUpdateRequest extends FormRequest
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
            'phone_number' => ['nullable', 'string'],
            'users' => ['nullable', 'array'],
            'warehouses' => ['nullable', 'array']
        ];
    }
}
