<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/** Shared rules for store + update, mirrors Livewire\Admin\Branches\Index::save(). */
class BranchRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:80',
            'pincode' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ];
    }
}
