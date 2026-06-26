<div>
    <x-admin.page-header title="Payments & collections" subtitle="All transactions plus outstanding balances across orders.">
        <button wire:click="openModal" class="btn-primary">+ Record payment</button>
    </x-admin.page-header>

    <div class="mb-5 grid gap-4 sm:grid-cols-2" data-reveal>
        <div class="glass rounded-3xl p-5">
            <p class="text-sm text-text-soft">Collected today</p>
            <p class="font-display text-3xl font-bold text-success">₹{{ number_format($todayTotal, 2) }}</p>
        </div>
        <div class="glass rounded-3xl p-5">
            <p class="text-sm text-text-soft">Total outstanding</p>
            <p class="font-display text-3xl font-bold text-warning">₹{{ number_format($dueTotal, 2) }}</p>
        </div>
    </div>

    <div class="glass rounded-3xl p-4 sm:p-5" data-reveal>
        <div class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-center">
            <div class="flex rounded-2xl border border-border p-1 dark:border-slate-700 w-max">
                <button wire:click="$set('tab', 'outstanding')"
                        class="rounded-xl px-4 py-1.5 text-sm font-semibold transition {{ $tab === 'outstanding' ? 'bg-gradient-to-r from-primary to-secondary text-white shadow' : 'text-text-soft' }}">
                    Outstanding
                </button>
                <button wire:click="$set('tab', 'payments')"
                        class="rounded-xl px-4 py-1.5 text-sm font-semibold transition {{ $tab === 'payments' ? 'bg-gradient-to-r from-primary to-secondary text-white shadow' : 'text-text-soft' }}">
                    Transactions
                </button>
            </div>
            @if ($tab === 'payments')
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Order no or customer…" class="input sm:max-w-xs">
                <select wire:model.live="methodFilter" class="input sm:max-w-44">
                    <option value="">All methods</option>
                    <option value="cash">Cash</option>
                    <option value="upi">UPI</option>
                    <option value="card">Card</option>
                    <option value="bank_transfer">Bank transfer</option>
                </select>
                <div class="lg:ml-auto">
                    <x-admin.date-filter :current="$period" />
                </div>
            @endif
        </div>

        <div class="mt-4 overflow-x-auto">
            @if ($tab === 'payments')
                <table class="table-admin table-cards min-w-[760px]">
                    <thead><tr><th>Order</th><th>Customer</th><th>Method</th><th>Type</th><th>Received by</th><th>When</th><th class="text-right">Amount</th></tr></thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr wire:key="pmt-{{ $payment->id }}">
                                <td>
                                    <a href="{{ route('admin.orders.show', $payment->order_id) }}" wire:navigate class="font-semibold text-primary hover:underline">
                                        {{ $payment->order?->order_no }}
                                    </a>
                                </td>
                                <td>{{ $payment->customer?->name }}</td>
                                <td><span class="badge badge-muted capitalize">{{ str_replace('_', ' ', $payment->method) }}</span></td>
                                <td class="capitalize text-text-soft">{{ $payment->type }}</td>
                                <td class="text-text-soft">{{ $payment->receivedBy?->name ?? 'System' }}</td>
                                <td class="text-xs text-text-soft">{{ $payment->created_at->format('d M, h:i A') }}</td>
                                <td class="text-right font-semibold text-success">+₹{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-10 text-center text-text-soft">No transactions yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $payments->links() }}</div>
            @else
                <table class="table-admin table-cards min-w-[680px]">
                    <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($outstanding as $order)
                            <tr wire:key="due-{{ $order->id }}">
                                <td class="font-semibold">{{ $order->order_no }}</td>
                                <td>
                                    <p>{{ $order->customer?->name }}</p>
                                    <p class="text-xs text-text-soft">{{ $order->customer?->mobile }}</p>
                                </td>
                                <td>₹{{ number_format($order->total, 2) }}</td>
                                <td class="text-success">₹{{ number_format($order->paid_amount, 2) }}</td>
                                <td class="font-semibold text-warning">₹{{ number_format($order->outstanding, 2) }}</td>
                                <td><span class="badge {{ $order->payment_status === 'partial' ? 'badge-warning' : 'badge-danger' }} capitalize">{{ $order->payment_status }}</span></td>
                                <td class="text-right">
                                    <button wire:click="openModal({{ $order->id }})" class="btn-primary">Collect</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-10 text-center text-text-soft"><x-icon name="check-badge" class="mx-auto mb-2 h-7 w-7 text-success" />Nothing outstanding.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $outstanding->links() }}</div>
            @endif
        </div>
    </div>

    <x-admin.modal show="showModal" title="Record a payment">
        <form wire:submit="recordPayment" class="space-y-4">
            @if ($selectedOrder)
                <div class="flex items-center justify-between rounded-2xl bg-gradient-to-r from-primary/10 to-secondary/10 px-4 py-3">
                    <div>
                        <p class="font-semibold">{{ $selectedOrder->order_no }}</p>
                        <p class="text-xs text-text-soft">{{ $selectedOrder->customer?->name }} · due ₹{{ number_format($selectedOrder->outstanding, 2) }}</p>
                    </div>
                    <button type="button" wire:click="$set('selectedOrderId', null)" class="text-xs font-semibold text-danger hover:underline">Change</button>
                </div>
            @else
                <div>
                    <label class="label">Find order</label>
                    <input type="search" wire:model.live.debounce.300ms="orderSearch" class="input" placeholder="Order no, customer name or mobile…">
                    @if ($dueOrders->isNotEmpty())
                        <div class="mt-2 space-y-1">
                            @foreach ($dueOrders as $due)
                                <button type="button" wire:click="selectOrder({{ $due->id }})"
                                        class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-sm transition hover:bg-primary/5">
                                    <span class="font-medium">{{ $due->order_no }} · {{ $due->customer?->name }}</span>
                                    <span class="text-xs font-semibold text-warning">₹{{ number_format($due->outstanding, 0) }} due</span>
                                </button>
                            @endforeach
                        </div>
                    @elseif (strlen($orderSearch) >= 2)
                        <p class="mt-2 text-xs text-text-soft">No orders with dues match that search.</p>
                    @endif
                </div>
            @endif
            @error('selectedOrderId') <p class="text-xs text-danger">{{ $message }}</p> @enderror

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">Amount (₹)</label>
                    <input type="number" step="0.01" wire:model="amount" class="input">
                    @error('amount') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Method</label>
                    <select wire:model="method" class="input">
                        <option value="cash">Cash</option>
                        <option value="upi">UPI</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank transfer</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="label">Reference (optional)</label>
                <input type="text" wire:model="reference" class="input" placeholder="UPI / gateway transaction ID">
            </div>
            <button class="btn-primary w-full justify-center" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="recordPayment">Record payment</span>
                <span wire:loading wire:target="recordPayment">Saving…</span>
            </button>
        </form>
    </x-admin.modal>
</div>
