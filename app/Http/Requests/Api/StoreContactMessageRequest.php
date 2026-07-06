<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/** Mirrors Livewire\Public\ContactSection (and the duplicate logic in Public\Home::submit()). */
class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|min:2|max:80',
            'email' => 'required|email',
            'phone' => 'required|min:8|max:15',
            'message' => 'required|min:10|max:2000',
        ];
    }
}
