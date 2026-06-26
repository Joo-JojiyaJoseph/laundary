<div>
    <x-admin.page-header title="Orders" subtitle="Every booking across the pipeline — updates land here live via Pusher.">
        <a href="{{ route('admin.pos') }}" wire:navigate class="btn-primary">+ New order (POS)</a>
    </x-admin.page-header>

    <div class="glass rounded-3xl p-4 sm:p-5" data-reveal>
        <div class="flex flex-col gap-3">
            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Order no, customer, mobile…" class="input sm:max-w-xs">
                <select wire:model.live="statusFilter" class="input sm:max-w-48">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
                <select wire:model.live="paymentFilter" class="input sm:max-w-40">
                    <option value="">All payments</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="partial">Partial</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
            <x-admin.date-filter :current="$period" />
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="table-admin table-cards min-w-[820px]">
                <thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Payment</th><th>Total</th><th>Due</th><th>Created</th><th></th></tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr wire:key="order-{{ $order->id }}">
                            <td>
                                <p class="font-semibold">{{ $order->order_no }}</p>
                                <p class="text-xs text-text-soft">{{ $order->branch?->name }}</p>
                            </td>
                            <td>
                                <p class="font-medium">{{ $order->customer?->name }}</p>
                                <p class="text-xs text-text-soft">{{ $order->customer?->mobile }}</p>
                            </td>
                            <td><span class="badge badge-primary"><x-icon :name="$order->status->icon()" class="h-3.5 w-3.5" /> {{ $order->status->label() }}</span></td>
                            <td>
                                <span class="badge {{ ['paid' => 'badge-success', 'partial' => 'badge-warning', 'unpaid' => 'badge-danger'][$order->payment_status] ?? 'badge-muted' }} capitalize">
                                    {{ $order->payment_status }}
                                </span>
                            </td>
                            <td class="font-semibold">₹{{ number_format($order->total, 0) }}</td>
                            <td class="{{ $order->outstanding > 0 ? 'text-warning font-semibold' : 'text-text-soft' }}">₹{{ number_format($order->outstanding, 0) }}</td>
                            <td class="text-xs text-text-soft">{{ $order->created_at->format('d M, h:i A') }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" wire:navigate class="btn-soft">Open</a>
                            </td>
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
