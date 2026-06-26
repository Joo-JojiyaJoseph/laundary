<div class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.customers.index') }}" class="btn-soft">← Back to customers</a>
        <a href="https://wa.me/91{{ preg_replace('/\D/', '', $customer->mobile) }}" target="_blank" class="btn-soft">WhatsApp</a>
    </div>

    {{-- Profile card --}}
    <div class="glass rounded-3xl p-6" data-reveal>
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
            <span class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-primary to-secondary text-xl font-bold text-white">
                {{ strtoupper(substr($customer->name, 0, 2)) }}
            </span>
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="font-display text-2xl font-bold">{{ $customer->name }}</h1>
                    @if ($customer->is_vip)<span class="badge badge-warning">VIP</span>@endif
                    <span class="badge badge-muted">{{ $customer->code }}</span>
                </div>
                <div class="mt-2 grid gap-x-6 gap-y-1 text-sm text-text-soft sm:grid-cols-2 lg:grid-cols-3">
                    <p><span class="font-medium text-text">Mobile:</span> {{ $customer->mobile }}</p>
                    <p><span class="font-medium text-text">Email:</span> {{ $customer->email ?? '—' }}</p>
                    <p><span class="font-medium text-text">City:</span> {{ $customer->city ?? '—' }}</p>
                    <p><span class="font-medium text-text">Loyalty:</span> {{ number_format($customer->loyalty_points) }} pts</p>
                    <p class="sm:col-span-2 lg:col-span-1"><span class="font-medium text-text">Address:</span> {{ $customer->address ?? '—' }}</p>
                </div>
                @if ($customer->notes)
                    <p class="mt-3 rounded-xl bg-amber-50 px-3 py-2 text-xs text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">Note: {{ $customer->notes }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Stat tiles --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" data-reveal>
        <div class="glass rounded-3xl p-5">
            <p class="text-sm text-text-soft">Total orders</p>
            <p class="font-display text-3xl font-bold">{{ number_format($totals['orders']) }}</p>
        </div>
        <div class="glass rounded-3xl p-5">
            <p class="text-sm text-text-soft">Total billed</p>
            <p class="font-display text-3xl font-bold">₹{{ number_format($totals['spent'], 2) }}</p>
        </div>
        <div class="glass rounded-3xl p-5">
            <p class="text-sm text-text-soft">Total paid</p>
            <p class="font-display text-3xl font-bold text-success">₹{{ number_format($totals['paid'], 2) }}</p>
        </div>
        <div class="glass rounded-3xl p-5">
            <p class="text-sm text-text-soft">Outstanding</p>
            <p class="font-display text-3xl font-bold text-warning">₹{{ number_format($totals['outstanding'], 2) }}</p>
        </div>
    </div>

    {{-- Orders & payments tabs --}}
    <div class="glass rounded-3xl p-4 sm:p-5" data-reveal>
        <div class="flex rounded-2xl border border-border p-1 dark:border-slate-700 w-max">
            <button wire:click="$set('tab', 'orders')"
                    class="rounded-xl px-4 py-1.5 text-sm font-semibold transition {{ $tab === 'orders' ? 'bg-gradient-to-r from-primary to-secondary text-white shadow' : 'text-text-soft' }}">
                Orders ({{ $totals['orders'] }})
            </button>
            <button wire:click="$set('tab', 'payments')"
                    class="rounded-xl px-4 py-1.5 text-sm font-semibold transition {{ $tab === 'payments' ? 'bg-gradient-to-r from-primary to-secondary text-white shadow' : 'text-text-soft' }}">
                Payments
            </button>
        </div>

        <div class="mt-4 overflow-x-auto">
            @if ($tab === 'orders')
                <table class="table-admin table-cards min-w-[640px]">
                    <thead><tr><th>Order</th><th>Date</th><th>Status</th><th>Total</th><th>Paid</th><th>Due</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr wire:key="cord-{{ $order->id }}">
                                <td class="font-semibold">{{ $order->order_no }}</td>
                                <td class="text-xs text-text-soft">{{ $order->created_at->format('d M Y') }}</td>
                                <td><span class="badge badge-primary capitalize">{{ $order->status->label() }}</span></td>
                                <td>₹{{ number_format($order->total, 2) }}</td>
                                <td class="text-success">₹{{ number_format($order->paid_amount, 2) }}</td>
                                <td class="font-semibold {{ $order->outstanding > 0 ? 'text-warning' : 'text-text-soft' }}">₹{{ number_format($order->outstanding, 2) }}</td>
                                <td class="text-right"><a href="{{ route('admin.orders.show', $order->id) }}" class="btn-soft">View</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-10 text-center text-text-soft">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <table class="table-admin table-cards min-w-[560px]">
                    <thead><tr><th>Order</th><th>Date</th><th>Method</th><th>Type</th><th class="text-right">Amount</th></tr></thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr wire:key="cpmt-{{ $payment->id }}">
                                <td>
                                    <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="font-semibold text-primary hover:underline">{{ $payment->order?->order_no ?? '—' }}</a>
                                </td>
                                <td class="text-xs text-text-soft">{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                                <td><span class="badge badge-muted capitalize">{{ str_replace('_', ' ', $payment->method) }}</span></td>
                                <td class="capitalize text-text-soft">{{ $payment->type }}</td>
                                <td class="text-right font-semibold {{ $payment->type === 'refund' ? 'text-danger' : 'text-success' }}">
                                    {{ $payment->type === 'refund' ? '-' : '+' }}₹{{ number_format($payment->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-text-soft">No payments yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
