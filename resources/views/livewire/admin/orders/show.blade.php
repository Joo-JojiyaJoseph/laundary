<div class="grid gap-5 xl:grid-cols-3">

    {{-- Left: status + items --}}
    <div class="xl:col-span-2 space-y-5">
        <div class="glass rounded-3xl p-6" data-reveal>
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h1 class="font-display text-xl font-bold">{{ $order->order_no }}</h1>
                    <p class="text-sm text-text-soft">{{ $order->branch?->name }} · {{ $order->created_at->format('d M Y, h:i A') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="badge badge-primary text-sm"><x-icon :name="$order->status->icon()" class="h-4 w-4" /> {{ $order->status->label() }}</span>
                    @if ($order->status !== \App\Enums\OrderStatus::Delivered)
                        <button wire:click="advanceStatus" class="btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="advanceStatus">Next stage →</span>
                            <span wire:loading wire:target="advanceStatus">Moving…</span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Pipeline --}}
            @php $currentIndex = array_search($order->status, $pipeline); @endphp
            <ol class="mt-6 flex flex-wrap gap-2">
                @foreach ($pipeline as $i => $status)
                    @php $reached = $currentIndex !== false && $i <= $currentIndex; @endphp
                    <li>
                        <button @click="$dispatch('confirm', { title: 'Change status?', message: 'Move this order to {{ $status->label() }}?', confirmText: 'Yes, continue', method: 'setStatus', params: ['{{ $status->value }}'] })"
                                class="flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold transition
                                       {{ $reached ? 'bg-gradient-to-r from-primary to-secondary text-white shadow-md shadow-primary/25' : 'border border-border text-text-soft hover:border-primary hover:text-primary dark:border-slate-700' }}">
                            <x-icon :name="$status->icon()" class="h-3.5 w-3.5" /> {{ $status->label() }}
                        </button>
                    </li>
                @endforeach
            </ol>

            {{-- Status history --}}
            @if ($order->statusLogs->isNotEmpty())
                <div class="mt-5 space-y-1.5">
                    @foreach ($order->statusLogs->sortByDesc('created_at')->take(8) as $log)
                        <div class="group flex items-center justify-between gap-2 text-xs text-text-soft" wire:key="log-{{ $log->id }}">
                            <p>• {{ \App\Enums\OrderStatus::tryFrom($log->status)?->label() ?? $log->status }} — {{ $log->created_at->format('d M, h:i A') }}</p>
                            <button @click="$dispatch('confirm', { title: 'Delete this status entry?', message: 'The order will revert to the previous status in the history.', confirmText: 'Yes, delete', method: 'deleteStatusLog', params: [{{ $log->id }}] })"
                                    class="opacity-0 transition group-hover:opacity-100 hover:text-danger" title="Delete entry">
                                <x-icon name="x-mark" class="h-3.5 w-3.5" />
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Items --}}
        <div class="glass rounded-3xl p-6" data-reveal>
            <h2 class="font-display text-lg font-semibold mb-4">Items ({{ $order->items->count() }})</h2>
            <div class="overflow-x-auto">
                <table class="table-admin table-cards">
                    <thead><tr><th>Item</th><th>Tag</th><th>Qty</th><th>Price</th><th class="text-right">Total</th></tr></thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr wire:key="item-{{ $item->id }}">
                                <td class="font-medium">{{ $item->product?->name ?? $item->name }}</td>
                                <td><span class="badge badge-muted font-mono">{{ $item->tag_code }}</span></td>
                                <td>{{ $item->qty }}</td>
                                <td>₹{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-right font-semibold">₹{{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 ml-auto max-w-xs space-y-1.5 text-sm">
                <div class="flex justify-between text-text-soft"><span>Subtotal</span><span>₹{{ number_format($order->subtotal, 2) }}</span></div>
                <div class="flex justify-between text-text-soft"><span>Discount</span><span>−₹{{ number_format($order->discount, 2) }}</span></div>
                <div class="flex justify-between text-text-soft"><span>Tax</span><span>₹{{ number_format($order->tax, 2) }}</span></div>
                <div class="flex justify-between border-t border-border/60 pt-2 font-display font-bold dark:border-slate-700"><span>Total</span><span>₹{{ number_format($order->total, 2) }}</span></div>
            </div>
        </div>

        {{-- Payments --}}
        <div class="glass rounded-3xl p-6" data-reveal>
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display text-lg font-semibold">Payments</h2>
                <span class="badge {{ $order->outstanding > 0 ? 'badge-warning' : 'badge-success' }}">
                    {{ $order->outstanding > 0 ? 'Due ₹' . number_format($order->outstanding, 2) : 'Fully paid' }}
                </span>
            </div>

            @if ($order->payments->isNotEmpty())
                <div class="space-y-2 mb-5">
                    @foreach ($order->payments as $payment)
                        <div class="flex items-center justify-between rounded-2xl bg-white/50 px-4 py-2.5 text-sm dark:bg-slate-800/50" wire:key="pay-{{ $payment->id }}">
                            <div>
                                <p class="font-medium capitalize">{{ str_replace('_', ' ', $payment->method) }} <span class="badge badge-muted ml-1 capitalize">{{ $payment->type }}</span></p>
                                <p class="text-xs text-text-soft">{{ $payment->created_at->format('d M, h:i A') }} · {{ $payment->receivedBy?->name ?? 'System' }} {{ $payment->reference ? '· ' . $payment->reference : '' }}</p>
                            </div>
                            <span class="font-semibold text-success">+₹{{ number_format($payment->amount, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($order->outstanding > 0)
                <form wire:submit="addPayment" class="grid gap-3 sm:grid-cols-4">
                    <div>
                        <label class="label">Amount (₹)</label>
                        <input type="number" step="0.01" wire:model="paymentAmount" class="input" placeholder="{{ number_format($order->outstanding, 2, '.', '') }}">
                        @error('paymentAmount') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label">Method</label>
                        <select wire:model="paymentMethod" class="input">
                            <option value="cash">Cash</option>
                            <option value="upi">UPI</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank transfer</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Reference (optional)</label>
                        <input type="text" wire:model="paymentReference" class="input" placeholder="Txn ID">
                    </div>
                    <div class="flex items-end">
                        <button class="btn-primary w-full justify-center" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="addPayment">Record</span>
                            <span wire:loading wire:target="addPayment">Saving…</span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    {{-- Right: customer, rider, tracking --}}
    <div class="space-y-5">
        <div class="glass rounded-3xl p-6" data-reveal>
            <h3 class="font-display text-sm font-semibold uppercase tracking-wider text-text-soft mb-3">Customer</h3>
            <p class="font-display text-lg font-semibold">{{ $order->customer?->name }}</p>
            <p class="text-sm text-text-soft">{{ $order->customer?->mobile }} · {{ $order->customer?->code }}</p>
            <div class="mt-3 flex gap-2">
                <a href="https://wa.me/91{{ preg_replace('/\D/', '', $order->customer?->mobile ?? '') }}" target="_blank" class="btn-soft flex-1 justify-center">WhatsApp</a>
                <a href="tel:{{ $order->customer?->mobile }}" class="btn-soft flex-1 justify-center">Call</a>
            </div>
        </div>

        <div class="glass rounded-3xl p-6" data-reveal>
            <h3 class="font-display text-sm font-semibold uppercase tracking-wider text-text-soft mb-3">Rider</h3>
            <div class="flex gap-2">
                <select wire:model="rider_id" class="input">
                    <option value="">Unassigned</option>
                    @foreach ($riders as $riderOption)
                        <option value="{{ $riderOption->id }}">{{ $riderOption->user?->name }} {{ $riderOption->is_online ? '· online' : '' }}</option>
                    @endforeach
                </select>
                <button wire:click="assignRider" class="btn-primary">Set</button>
            </div>
            @if ($order->delivery_otp)
                <p class="mt-3 text-xs text-text-soft">Delivery OTP: <span class="font-mono font-bold text-text dark:text-white">{{ $order->delivery_otp }}</span></p>
            @endif
        </div>

        @if ($order->invoice)
            <div class="glass rounded-3xl p-6" data-reveal>
                <h3 class="font-display text-sm font-semibold uppercase tracking-wider text-text-soft mb-3">Invoice & tracking</h3>
                <p class="font-semibold">{{ $order->invoice->invoice_no }}</p>
                @php $trackingUrl = $order->invoice->trackingUrl(); @endphp
                <a href="{{ $trackingUrl }}" target="_blank" class="mt-1 block break-all text-xs text-primary hover:underline">{{ $trackingUrl }}</a>
                <div class="mt-4 grid place-items-center rounded-2xl bg-white p-4">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate($trackingUrl) !!}
                </div>
                <p class="mt-3 text-center text-xs text-text-soft">Print this QR on the invoice — customers track without logging in.</p>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <a href="{{ $this->whatsappShareUrl }}" target="_blank"
                       class="btn-soft justify-center !border-success/40 !text-success hover:!border-success">
                        <x-icon name="chat-bubble-left-right" class="h-4 w-4" /> Share
                    </a>
                    <button wire:click="sendInvoiceViaApi" wire:loading.attr="disabled" class="btn-primary justify-center text-xs">
                        <span wire:loading.remove wire:target="sendInvoiceViaApi">Send via API</span>
                        <span wire:loading wire:target="sendInvoiceViaApi">Sending…</span>
                    </button>
                </div>
                <p class="mt-2 text-center text-[11px] text-text-soft">Share opens WhatsApp with the invoice summary &amp; tracking link prefilled. "Send via API" uses the WhatsApp Cloud API directly.</p>
            </div>
        @endif

        @if ($order->notes)
            <div class="glass rounded-3xl p-6" data-reveal>
                <h3 class="font-display text-sm font-semibold uppercase tracking-wider text-text-soft mb-2">Notes</h3>
                <p class="text-sm">{{ $order->notes }}</p>
            </div>
        @endif
    </div>
</div>
