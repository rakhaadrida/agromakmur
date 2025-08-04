<?php

namespace App\Http\Requests;

use App\Rules\ValidProductSubcategory;
use Illuminate\Foundation\Http\FormRequest;

class ProductTransferCancelRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'description' => ['required', 'string'],
        ];
    }
}
