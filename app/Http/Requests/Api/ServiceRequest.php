<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/** Mirrors Livewire\Admin\Services\Index::save() (slug is generated in the controller). */
class ServiceRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'product_category_id' => 'nullable|exists:product_categories,id',
        ];
    }
}
