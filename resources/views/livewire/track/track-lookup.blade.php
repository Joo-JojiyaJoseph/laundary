<div class="relative mx-auto max-w-5xl px-6 py-20">
    <div class="grid items-center gap-12 lg:grid-cols-2">

        <div data-hero>
            <p class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50/70 px-4 py-1.5 text-xs font-semibold text-primary-700">
                <span class="relative flex h-2 w-2"><span class="animate-ping absolute h-full w-full rounded-full bg-primary opacity-75"></span><span class="relative h-2 w-2 rounded-full bg-primary"></span></span>
                Live order tracking
            </p>
            <h1 class="mt-5 font-display text-4xl font-bold leading-tight sm:text-5xl">Where's my laundry?</h1>
            <p class="mt-4 max-w-md text-lg text-slate-500">
                Enter the order number from your bill (or scan the QR on it) and the mobile
                number you booked with — we'll show you exactly which stage your garments are in,
                live.
            </p>

            <form wire:submit="track" class="mt-8 max-w-md space-y-4">
                <div>
                    <label class="label">Order number</label>
                    <input type="text" wire:model="orderNo" class="input uppercase" placeholder="LDS00123">
                    @error('orderNo') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Registered mobile</label>
                    <input type="tel" wire:model="mobile" class="input" placeholder="98470 12345">
                    @error('mobile') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <button class="btn-primary w-full justify-center" data-magnetic wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="track">Track my order →</span>
                    <span wire:loading wire:target="track" class="flex items-center gap-2">
                        <span class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Searching…
                    </span>
                </button>
            </form>
            <p class="mt-4 text-xs text-text-soft">Tip: scanning the QR code on your invoice opens this tracking page instantly — no typing needed.</p>
        </div>

        {{-- Animated tracking illustration --}}
        <div class="relative" data-hero data-tilt="6">
            <span class="tilt-glare"></span>
            <div class="glass rounded-[2rem] p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-text-soft">Order</p>
                        <p class="font-display text-xl font-bold">ORD-004812</p>
                    </div>
                    <span class="badge badge-primary">Live</span>
                </div>
                <ol class="mt-6 space-y-4">
                    @foreach ([
                        ['icon' => 'truck', 'label' => 'Picked up · 9:40 AM', 'done' => true],
                        ['icon' => 'sparkles', 'label' => 'Washing — eco cycle', 'done' => true],
                        ['icon' => 'fire', 'label' => 'Steam ironing', 'done' => true, 'active' => true],
                        ['icon' => 'shield-check', 'label' => 'Quality check', 'done' => false],
                        ['icon' => 'home', 'label' => 'Delivered to your door', 'done' => false],
                    ] as $step)
                        <li class="flex items-center gap-3">
                            <span @class([
                                'grid h-9 w-9 place-items-center rounded-full transition',
                                'bg-gradient-to-br from-primary to-secondary text-white shadow-md shadow-primary/30' => $step['done'],
                                'bg-border/60 text-text-soft' => ! $step['done'],
                            ])>
                                <x-icon :name="$step['icon']" class="h-4 w-4" />
                            </span>
                            <span @class(['text-sm font-medium', 'text-text-soft' => ! $step['done']])>{{ $step['label'] }}</span>
                            @if ($step['active'] ?? false)
                                <span class="relative ml-auto flex h-2.5 w-2.5"><span class="animate-ping absolute h-full w-full rounded-full bg-primary opacity-75"></span><span class="relative h-2.5 w-2.5 rounded-full bg-primary"></span></span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</div>
