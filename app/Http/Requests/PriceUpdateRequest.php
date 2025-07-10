<?php

namespace App\Http\Requests;

use App\Rules\ValidUniquePriceCode;
use Illuminate\Foundation\Http\FormRequest;

class PriceUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', new ValidUniquePriceCode($this->route('id'))],
        ];
    }
}
