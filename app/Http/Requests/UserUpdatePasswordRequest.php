<?php

namespace App\Http\Requests;

use App\Rules\ValidUserPassword;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdatePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'current_password' => ['required', 'string', new ValidUserPassword()],
            'new_password' => ['required', 'confirmed'],
        ];
    }
}
