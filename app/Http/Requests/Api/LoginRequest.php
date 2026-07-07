<?php

namespace App\Http\Requests\Api;

/**
 * No Rule::exists() here on purpose — checking "does this email exist"
 * separately from the password is a user-enumeration vulnerability.
 * Auth::attempt() in the controller is the single source of truth for
 * whether credentials are valid.
 */
class LoginRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }
}