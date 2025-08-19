<?php

namespace App\Http\Requests;

use App\Rules\ValidPriceType;
use App\Rules\ValidUniquePriceCode;
use Illuminate\Foundation\Http\FormRequest;

class PriceCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', new ValidUniquePriceCode(0)],
            'type' => ['required', 'string', new ValidPriceType()]
        ];
    }
}
