<?php

namespace App\Http\Requests;

use App\Rules\ValidUserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($this->route('user'))],
            'role' => ['required', 'string', new ValidUserRole()],
        ];
    }
}
