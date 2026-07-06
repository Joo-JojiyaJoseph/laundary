<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Order;

/**
 * Mirrors the validation used in both
 * Livewire\Admin\Orders\Show::addPayment() and Livewire\Admin\Payments\Index::recordPayment().
 * The "max" cap on amount is set dynamically against the order's outstanding balance.
 */
class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $orderId = $this->input('order_id') ?? $this->route('order')?->id;
        $order = $orderId ? Order::find($orderId) : null;

        return [
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01|max:' . max(0.01, (float) ($order?->outstanding ?? 999999999)),
            'method' => 'required|in:cash,upi,card,bank_transfer',
            'reference' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'Pick an order first.',
        ];
    }
}
