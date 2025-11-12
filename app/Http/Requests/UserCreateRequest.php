<?php

namespace App\Http\Requests;

use App\Rules\ValidUserRole;
use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'confirmed', 'string', 'min:6'],
            'role' => ['required', 'string', new ValidUserRole()],
            'branch_ids' => ['nullable', 'array']
        ];
    }
}
