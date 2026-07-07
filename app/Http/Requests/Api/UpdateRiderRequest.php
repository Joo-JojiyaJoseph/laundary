<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/** Mirrors Livewire\Admin\Riders\Index::save() for the "edit" path (password optional). */
class UpdateRiderRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rider = $this->route('rider');
        $userId = $rider?->user_id;

        return [
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:150|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:8',
            'vehicle_number' => 'nullable|string|max:20',
            'branch_id' => 'nullable|exists:branches,id',
        ];
    }
}
