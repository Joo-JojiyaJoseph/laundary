<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * POS checkout — mirrors Livewire\Pos\Terminal::checkout().
 * The cart line items are validated per-item in PosController::checkout()
 * since the shape (product_id, qty, ...) is nested and easier to check there.
 */
class CheckoutRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'cart' => 'required|array|min:1',
            'cart.*.product_id' => 'required|exists:products,id',
            'cart.*.qty' => 'required|numeric|min:0.01',
            'discount' => 'nullable|numeric|min:0',
            'advance' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,upi,card,bank_transfer',
            'notes' => 'nullable|string|max:2000',
            'pickup_date' => 'required|date',
            'delivery_date' => 'required|date|after_or_equal:pickup_date',
            'delivery_time' => 'required|date_format:H:i',
            'delivery_address' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Select a customer first.',
            'cart.required' => 'Cart is empty.',
        ];
    }
}
