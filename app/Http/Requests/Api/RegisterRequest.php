<?php
// app/Http/Requests/Api/RegisterRequest.php

namespace App\Http\Requests\Api;

class RegisterRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            // password_confirmation field required alongside this, per |confirmed
        ];
    }
}