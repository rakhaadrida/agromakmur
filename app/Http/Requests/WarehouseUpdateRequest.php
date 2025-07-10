<?php

namespace App\Http\Requests;

use App\Rules\ValidWarehouseType;
use Illuminate\Foundation\Http\FormRequest;

class WarehouseUpdateRequest extends FormRequest
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
            'type' => ['required', 'string', new ValidWarehouseType()]
        ];
    }
}
