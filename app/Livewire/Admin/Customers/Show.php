<?php

namespace App\Livewire\Admin\Customers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use Livewire\Component;

class Show extends Component
{
    public Customer $customer;

    /** orders|payments */
    public string $tab = 'orders';

    public function mount(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function render()
    {
        $orders = $this->customer->orders()
            ->with('branch')
            ->latest()
            ->take(50)
            ->get();

        $payments = $this->customer->payments()
            ->with('order')
            ->latest()
            ->take(50)
            ->get();

        $totals = [
            'orders' => $this->customer->orders()->count(),
            'spent' => (float) $this->customer->orders()->sum('total'),
            'paid' => (float) $this->customer->payments()->where('type', 'payment')->sum('amount'),
            'outstanding' => (float) $this->customer->orders()
                ->whereIn('payment_status', ['unpaid', 'partial'])
                ->selectRaw('COALESCE(SUM(total - paid_amount), 0) as due')
                ->value('due'),
        ];

        return view('livewire.admin.customers.show', compact('orders', 'payments', 'totals'))
            ->layout('layouts.admin', ['title' => $this->customer->name]);
    }
}
