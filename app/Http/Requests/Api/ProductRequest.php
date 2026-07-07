<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/** Mirrors Livewire\Admin\Products\Index::save(). */
class ProductRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:product_categories,id',
            'service_id' => 'required|exists:services,id',
            'name' => 'required|string|max:100',
            'uom' => 'required|in:pc,kg,pair,set,mtr',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id' => 'category',
            'service_id' => 'service',
        ];
    }
}
