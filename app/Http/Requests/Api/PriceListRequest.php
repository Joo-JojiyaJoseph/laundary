<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/** Mirrors Livewire\Admin\PriceLists\Index::save(). */
class PriceListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:branch,customer,vip,seasonal,promo',
            'branch_id' => 'nullable|exists:branches,id|required_if:type,branch',
            'customer_id' => 'nullable|exists:customers,id|required_if:type,customer',
            'price' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
        ];
    }
}
