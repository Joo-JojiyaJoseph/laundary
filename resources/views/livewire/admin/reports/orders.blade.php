<div>
    <x-admin.page-header title="Order report" subtitle="Filter orders by date, customer, rider and status — then export.">
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
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <select wire:model.live="customerFilter" class="input">
                    <option value="">All customers</option>
                    @foreach ($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="riderFilter" class="input">
                    <option value="">All riders</option>
                    @foreach ($riders as $r)
                        <option value="{{ $r->id }}">{{ $r->user?->name ?? 'Rider #' . $r->id }}</option>
                    @endforeach
                </select>
                <select wire:model.live="statusFilter" class="input">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
                <select wire:model.live="paymentFilter" class="input">
                    <option value="">All payment states</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="partial">Partial</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Summary --}}
    <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" data-reveal>
        <div class="glass rounded-3xl p-5">
            <p class="text-sm text-text-soft">Orders</p>
            <p class="font-display text-3xl font-bold">{{ number_format($summary['count']) }}</p>
            <p class="mt-1 text-xs text-text-soft">{{ $this->periodLabel() }}</p>
        </div>
        <div class="glass rounded-3xl p-5">
            <p class="text-sm text-text-soft">Total billed</p>
            <p class="font-display text-3xl font-bold">₹{{ number_format($summary['total'], 2) }}</p>
        </div>
        <div class="glass rounded-3xl p-5">
            <p class="text-sm text-text-soft">Collected</p>
            <p class="font-display text-3xl font-bold text-success">₹{{ number_format($summary['paid'], 2) }}</p>
        </div>
        <div class="glass rounded-3xl p-5">
            <p class="text-sm text-text-soft">Outstanding</p>
            <p class="font-display text-3xl font-bold text-warning">₹{{ number_format($summary['outstanding'], 2) }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="glass mt-5 rounded-3xl p-4 sm:p-5" data-reveal>
        <div class="overflow-x-auto">
            <table class="table-admin table-cards min-w-[820px]">
                <thead><tr><th>Order</th><th>Date</th><th>Customer</th><th>Rider</th><th>Status</th><th>Total</th><th>Paid</th><th>Due</th></tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr wire:key="rord-{{ $order->id }}">
                            <td><a href="{{ route('admin.orders.show', $order->id) }}" class="font-semibold text-primary hover:underline">{{ $order->order_no }}</a></td>
                            <td class="text-xs text-text-soft">{{ $order->created_at->format('d M Y') }}</td>
                            <td>{{ $order->customer?->name ?? '—' }}</td>
                            <td class="text-text-soft">{{ $order->rider?->user?->name ?? '—' }}</td>
                            <td><span class="badge badge-primary capitalize">{{ $order->status->label() }}</span></td>
                            <td>₹{{ number_format($order->total, 2) }}</td>
                            <td class="text-success">₹{{ number_format($order->paid_amount, 2) }}</td>
                            <td class="font-semibold {{ $order->outstanding > 0 ? 'text-warning' : 'text-text-soft' }}">₹{{ number_format($order->outstanding, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-10 text-center text-text-soft">No orders match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $orders->links() }}</div>
    </div>
</div>
