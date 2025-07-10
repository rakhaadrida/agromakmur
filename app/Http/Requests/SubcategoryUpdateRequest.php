<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubcategoryUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'reminder_limit' => ['required', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id,deleted_at,NULL'],
        ];
    }
}
