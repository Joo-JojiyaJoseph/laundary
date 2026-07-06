<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Mirrors Livewire\Admin\Customers\Index::save() for the "edit" path — the
 * unique rules ignore the current record, exactly like ->ignore($this->editingId).
 */
class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer');

        return [
            'name' => 'required|string|max:120',
            'mobile' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{8,20}$/', Rule::unique('customers', 'mobile')->ignore($customerId)],
            'address' => 'required|string|max:500',
            'alternate_mobile' => 'nullable|string|max:20',
            'email' => ['nullable', 'email', 'max:150', Rule::unique('customers', 'email')->ignore($customerId)],
            'birthday' => 'nullable|date|before:today',
            'city' => 'nullable|string|max:80',
            'pincode' => 'nullable|string|max:10',
            'branch_id' => 'nullable|exists:branches,id',
            'notes' => 'nullable|string|max:2000',
            'is_vip' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.unique' => 'A customer with this mobile number already exists.',
            'mobile.regex' => 'Please enter a valid mobile number.',
            'email.unique' => 'A customer with this email already exists.',
            'address.required' => 'Address is required.',
        ];
    }
}
