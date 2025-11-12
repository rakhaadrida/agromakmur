<?php

namespace App\Http\Requests;

use App\Rules\ValidWarehouseType;
use Illuminate\Foundation\Http\FormRequest;

class WarehouseCreateRequest extends FormRequest
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
            'type' => ['required', 'string', new ValidWarehouseType()],
            'branch_ids' => ['array']
        ];
    }
}
