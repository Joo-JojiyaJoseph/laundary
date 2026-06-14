<div>
    {{-- Tabs --}}
    <div class="grid grid-cols-3 gap-2 rounded-2xl border border-border bg-white p-1.5">
        @foreach (['deliveries' => 'Deliveries', 'done' => 'Done today'] as $key => $label)
            <button wire:click="$set('tab', '{{ $key }}')"
                    class="rounded-xl px-3 py-2.5 text-sm font-bold transition
                           {{ $tab === $key ? 'bg-gradient-to-r from-primary to-secondary text-white shadow-md shadow-primary/25' : 'text-text-soft' }}">
                {{ $label }}
                <span class="ml-1 rounded-full px-1.5 text-xs {{ $tab === $key ? 'bg-white/25' : 'bg-border/60' }}">
                    {{ ${$key}->count() }}
                </span>
            </button>
        @endforeach
    </div>

    <div class="mt-4 space-y-4">
        @if ($tab === 'pickups')
            @forelse ($pickups as $order)
                <div class="glass rounded-3xl p-5" wire:key="pk-{{ $order->id }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-display text-lg font-bold">{{ $order->customer?->name }}</p>
                            <p class="text-sm text-text-soft">{{ $order->order_no }} · pickup {{ $order->pickup_at?->format('d M, h:i A') ?? 'today' }}</p>
                        </div>
                        <span class="badge badge-warning whitespace-nowrap">Pickup</span>
                    </div>
                    @if ($order->delivery_address || $order->customer?->city)
                        <p class="mt-3 flex items-start gap-2 rounded-2xl bg-white/60 p-3 text-sm">
                            <x-icon name="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                            {{ $order->delivery_address ?: $order->customer?->city }}
                        </p>
                    @endif
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <a href="tel:{{ $order->customer?->mobile }}" class="btn-soft justify-center py-3 text-sm">
                            <x-icon name="phone" class="h-4 w-4" /> Call
                        </a>
                        <button wire:click="markPickedUp({{ $order->id }})" wire:loading.attr="disabled"
                                class="btn-primary justify-center py-3 text-sm">
                            Picked up ✓
                        </button>
                    </div>
                </div>
            @empty
                <div class="glass rounded-3xl p-10 text-center text-text-soft">
                    <x-icon name="check-badge" class="mx-auto mb-2 h-8 w-8 text-success" />
                    No pickups pending. Great job!
                </div>
            @endforelse
        @elseif ($tab === 'deliveries')
            @forelse ($deliveries as $order)
                <div class="glass rounded-3xl p-5" wire:key="dl-{{ $order->id }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-display text-lg font-bold">{{ $order->customer?->name }}</p>
                            <p class="text-sm text-text-soft">{{ $order->order_no }} · ₹{{ number_format($order->total, 0) }}
                                @if ($order->outstanding > 0) · <span class="font-semibold text-warning">collect ₹{{ number_format($order->outstanding, 0) }}</span> @endif
                            </p>
                        </div>
                        <span class="badge badge-primary whitespace-nowrap">{{ $order->status->label() }}</span>
                    </div>
                    <p class="mt-3 flex items-start gap-2 rounded-2xl bg-white/60 p-3 text-sm">
                        <x-icon name="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                        {{ $order->delivery_address ?: ($order->customer?->city ?? 'Address with branch') }}
                        <span class="ml-auto whitespace-nowrap text-xs text-text-soft">by {{ $order->delivery_expected_at?->format('h:i A') }}</span>
                    </p>
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <a href="tel:{{ $order->customer?->mobile }}" class="btn-soft justify-center py-3 text-sm">
                            <x-icon name="phone" class="h-4 w-4" /> Call
                        </a>
                        @if ($order->status === \App\Enums\OrderStatus::Ready)
                            <button wire:click="startDelivery({{ $order->id }})" class="btn-primary justify-center py-3 text-sm">
                                Start delivery →
                            </button>
                        @else
                            <a target="_blank" href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($order->delivery_address ?: ($order->customer?->city ?? 'Kochi')) }}"
                               class="btn-soft justify-center py-3 text-sm">
                                <x-icon name="map-pin" class="h-4 w-4" /> Navigate
                            </a>
                        @endif
                    </div>
                    @if ($order->status === \App\Enums\OrderStatus::OutForDelivery)
                        <div class="mt-3 flex gap-2">
                            <input type="text" inputmode="numeric" maxlength="6" wire:model="otp.{{ $order->id }}"
                                   placeholder="Customer OTP {{ $order->delivery_otp ? '' : '(none set)' }}"
                                   class="input flex-1 text-center font-mono text-lg tracking-[.4em]">
                            <button wire:click="markDelivered({{ $order->id }})" wire:loading.attr="disabled"
                                    class="btn-primary px-5 text-sm">Delivered ✓</button>
                        </div>
                        @error("otp.{$order->id}") <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p> @enderror
                    @endif
                </div>
            @empty
                <div class="glass rounded-3xl p-10 text-center text-text-soft">
                    <x-icon name="truck" class="mx-auto mb-2 h-8 w-8 text-primary" />
                    Nothing out for delivery right now.
                </div>
            @endforelse
        @else
            @forelse ($done as $order)
                <div class="flex items-center justify-between rounded-2xl border border-border bg-white px-4 py-3" wire:key="dn-{{ $order->id }}">
                    <div>
                        <p class="text-sm font-semibold">{{ $order->customer?->name }}</p>
                        <p class="text-xs text-text-soft">{{ $order->order_no }} · {{ $order->delivered_at?->format('h:i A') }}</p>
                    </div>
                    <span class="badge badge-success">Delivered</span>
                </div>
            @empty
                <div class="glass rounded-3xl p-10 text-center text-text-soft">No deliveries completed yet today.</div>
            @endforelse
        @endif
    </div>
</div>
