<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/** Mirrors Livewire\Admin\Riders\Index::save() for the "create" (new user) path. */
class StoreRiderRequest extends ApiFormRequest
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
            'password' => 'required|string|min:8',
            'vehicle_number' => 'nullable|string|max:20',
            'branch_id' => 'nullable|exists:branches,id',
        ];
    }
}
