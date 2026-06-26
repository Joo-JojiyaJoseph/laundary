<div>
    <x-admin.page-header title="Payment report" subtitle="Filter collections by date, customer, method and type — then export.">
        <button wire:click="export" class="btn-soft" wire:loading.attr="disabled" wire:target="export">
            <x-icon name="archive-box-arrow-down" class="h-4 w-4" />
            <span wire:loading.remove wire:target="export">Export Excel</span>
            <span wire:loading wire:target="export">Preparing…</span>
        </button>
    </x-admin.page-header>

    {{-- Filters --}}
    <div class="glass rounded-3xl p-4 sm:p-5" data-reveal>
        <div class="space-y-3">
            <x-admin.date-filter />
            <div class="grid gap-3 sm:grid-cols-3">
                <select wire:model.live="customerFilter" class="input">
                    <option value="">All customers</option>
                    @foreach ($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="methodFilter" class="input">
                    <option value="">All methods</option>
                    <option value="cash">Cash</option>
                    <option value="upi">UPI</option>
                    <option value="card">Card</option>
                    <option value="bank_transfer">Bank transfer</option>
                </select>
                <select wire:model.live="typeFilter" class="input">
                    <option value="">All types</option>
                    <option value="payment">Payment</option>
                    <option value="advance">Advance</option>
                    <option value="refund">Refund</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Summary --}}
    <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" data-reveal>
        <div class="glass rounded-3xl p-5">
            <p class="text-sm text-text-soft">Transactions</p>
            <p class="font-display text-3xl font-bold">{{ number_format($summary['count']) }}</p>
            <p class="mt-1 text-xs text-text-soft">{{ $this->periodLabel() }}</p>
        </div>
        <div class="glass rounded-3xl p-5">
            <p class="text-sm text-text-soft">Collected</p>
            <p class="font-display text-3xl font-bold text-success">₹{{ number_format($summary['collected'], 2) }}</p>
        </div>
        <div class="glass rounded-3xl p-5">
            <p class="text-sm text-text-soft">Refunded</p>
            <p class="font-display text-3xl font-bold text-danger">₹{{ number_format($summary['refunded'], 2) }}</p>
        </div>
        <div class="glass rounded-3xl p-5">
            <p class="text-sm text-text-soft">Net</p>
            <p class="font-display text-3xl font-bold">₹{{ number_format($summary['net'], 2) }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="glass mt-5 rounded-3xl p-4 sm:p-5" data-reveal>
        <div class="overflow-x-auto">
            <table class="table-admin table-cards min-w-[820px]">
                <thead><tr><th>Date</th><th>Order</th><th>Customer</th><th>Method</th><th>Type</th><th>Received by</th><th class="text-right">Amount</th></tr></thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr wire:key="rpmt-{{ $payment->id }}">
                            <td class="text-xs text-text-soft">{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                            <td><a href="{{ route('admin.orders.show', $payment->order_id) }}" class="font-semibold text-primary hover:underline">{{ $payment->order?->order_no ?? '—' }}</a></td>
                            <td>{{ $payment->customer?->name ?? '—' }}</td>
                            <td><span class="badge badge-muted capitalize">{{ str_replace('_', ' ', $payment->method) }}</span></td>
                            <td class="capitalize text-text-soft">{{ $payment->type }}</td>
                            <td class="text-text-soft">{{ $payment->receivedBy?->name ?? 'System' }}</td>
                            <td class="text-right font-semibold {{ $payment->type === 'refund' ? 'text-danger' : 'text-success' }}">
                                {{ $payment->type === 'refund' ? '-' : '+' }}₹{{ number_format($payment->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-text-soft">No payments match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $payments->links() }}</div>
    </div>
</div>
