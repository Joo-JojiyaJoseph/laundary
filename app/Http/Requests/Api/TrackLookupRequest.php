<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/** Mirrors Livewire\Track\TrackLookup / TrackLookupForm. */
class TrackLookupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_no' => 'required|string|max:30',
            'mobile' => 'required|string|min:8|max:15',
        ];
    }

    public function messages(): array
    {
        return [
            'order_no' => 'order number',
            'mobile' => 'mobile number',
        ];
    }
}
