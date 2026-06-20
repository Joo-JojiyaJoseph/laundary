<div class="grid gap-5 xl:grid-cols-5">

    {{-- Left: catalogue --}}
    <div class="xl:col-span-3 space-y-5">

        {{-- Customer --}}
        <div class="glass relative z-30 rounded-3xl p-5" data-reveal>
            <div class="flex items-center justify-between">
                <label class="text-xs font-semibold uppercase tracking-wider text-text-soft">Customer</label>
                @unless ($customer)
                    <button wire:click="$toggle('showQuickAdd')" class="btn-soft">
                        {{ $showQuickAdd ? 'Search instead' : '+ Quick add' }}
                    </button>
                @endunless
            </div>

            @if ($customer)
                <div class="mt-3 flex items-center justify-between rounded-2xl bg-gradient-to-r from-primary/10 to-secondary/10 p-3">
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-full bg-gradient-to-br from-primary to-secondary text-sm font-bold text-white shadow-md shadow-primary/30">
                            {{ strtoupper(substr($customer->name, 0, 2)) }}
                        </span>
                        <div>
                            <p class="font-semibold leading-tight">{{ $customer->name }}</p>
                            <p class="text-xs text-text-soft">{{ $customer->mobile }} · {{ $customer->code }}</p>
                        </div>
                        <!-- <span class="badge {{ ['silver' => 'badge-muted', 'gold' => 'badge-warning', 'platinum' => 'badge-primary'][$customer->loyalty_tier ?? 'silver'] ?? 'badge-muted' }} capitalize ml-1">{{ $customer->loyalty_tier ?? 'silver' }}</span> -->
                        @if ($customer->is_vip) <span class="badge badge-warning">VIP</span> @endif
                    </div>
                    <button wire:click="clearCustomer" class="btn-soft">Change</button>
                </div>
            @elseif ($showQuickAdd)
                <div class="mt-3 grid gap-3 sm:grid-cols-[1fr_1fr_auto]">
                    <div>
                        <input type="text" wire:model="newName" class="input" placeholder="Customer name">
                        @error('newName') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <input type="tel" wire:model="newMobile" class="input" placeholder="Mobile number">
                        @error('newMobile') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                    <button wire:click="quickAddCustomer" class="btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="quickAddCustomer">Add &amp; select</span>
                        <span wire:loading wire:target="quickAddCustomer">Adding…</span>
                    </button>
                </div>
            @else
                <div class="relative mt-3">
                    <input type="search" wire:model.live.debounce.300ms="customerSearch"
                           placeholder="Search by name, mobile or code…"
                           class="input">
                    @if (strlen($customerSearch) >= 2)
                        <div class="absolute left-0 right-0 z-50 mt-2 max-h-72 w-full overflow-y-auto rounded-2xl border border-border bg-white shadow-2xl ring-1 ring-black/5">
                            @forelse ($customers as $result)
                                <button wire:click="selectCustomer({{ $result->id }})"
                                        class="flex w-full items-center gap-3 px-4 py-3 text-left transition hover:bg-primary/5">
                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-primary to-secondary text-xs font-bold text-white">
                                        {{ strtoupper(substr($result->name, 0, 2)) }}
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-medium">{{ $result->name }}</span>
                                        <span class="block text-xs text-text-soft">{{ $result->mobile }} · {{ $result->code }}</span>
                                    </span>
                                    <!-- <span class="badge badge-muted ml-auto capitalize">{{ $result->loyalty_tier ?? 'silver' }}</span> -->
                                </button>
                            @empty
                                <div class="px-4 py-4 text-sm text-text-soft">
                                    No customer found —
                                    <button wire:click="$set('showQuickAdd', true)" class="font-semibold text-primary hover:underline">add "{{ $customerSearch }}" now</button>
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
            @endif
            @error('customerId') <p class="mt-2 text-xs font-medium text-danger">{{ $message }}</p> @enderror
        </div>

        {{-- Category + service filters + products --}}
        <div class="glass relative z-10 rounded-3xl p-5" data-reveal>
            {{-- Category filter --}}
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-text-soft">Category</p>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="$set('categoryFilter', null)"
                            @class([
                                'rounded-full px-4 py-1.5 text-xs font-semibold transition',
                                'bg-gradient-to-r from-primary to-secondary text-white shadow-md shadow-primary/25' => ! $categoryFilter,
                                'border border-border text-text-soft hover:border-primary hover:text-primary dark:border-slate-700' => $categoryFilter,
                            ])>All</button>
                    @foreach ($categories as $category)
                        <button wire:click="$set('categoryFilter', {{ $category->id }})"
                                @class([
                                    'rounded-full px-4 py-1.5 text-xs font-semibold transition',
                                    'bg-gradient-to-r from-primary to-secondary text-white shadow-md shadow-primary/25' => $categoryFilter == $category->id,
                                    'border border-border text-text-soft hover:border-primary hover:text-primary dark:border-slate-700' => $categoryFilter != $category->id,
                                ])>{{ $category->name }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Service filter (narrows to the chosen category) --}}
            <div class="mt-4">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-text-soft">Service</p>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="$set('serviceFilter', null)"
                            @class([
                                'rounded-full px-4 py-1.5 text-xs font-semibold transition',
                                'bg-gradient-to-r from-primary to-secondary text-white shadow-md shadow-primary/25' => ! $serviceFilter,
                                'border border-border text-text-soft hover:border-primary hover:text-primary dark:border-slate-700' => $serviceFilter,
                            ])>All</button>
                    @forelse ($services as $service)
                        <button wire:click="$set('serviceFilter', {{ $service->id }})"
                                @class([
                                    'rounded-full px-4 py-1.5 text-xs font-semibold transition',
                                    'bg-gradient-to-r from-primary to-secondary text-white shadow-md shadow-primary/25' => $serviceFilter === $service->id,
                                    'border border-border text-text-soft hover:border-primary hover:text-primary dark:border-slate-700' => $serviceFilter !== $service->id,
                                ])>{{ $service->name }}</button>
                    @empty
                        <span class="px-1 py-1.5 text-xs text-text-soft">No services in this category.</span>
                    @endforelse
                </div>
            </div>

            <input type="search" wire:model.live.debounce.300ms="productSearch" placeholder="Search products…"
                   class="mt-4 w-full rounded-2xl border border-border bg-white/70 px-4 py-2.5 text-sm outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/10 dark:border-slate-700 dark:bg-slate-800/70">

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($products as $product)
                    @php $inCart = isset($cart[$product->id]); @endphp
                    <button wire:click="addToCart({{ $product->id }})"
                            @class([
                                'card-float group relative rounded-2xl border p-4 text-left transition',
                                'border-primary bg-primary/5 ring-2 ring-primary/30' => $inCart,
                                'border-border bg-white/60 hover:border-primary/40 dark:border-slate-700 dark:bg-slate-800/60' => ! $inCart,
                            ])>
                        @if ($inCart)
                            <span class="absolute right-2 top-2 grid h-6 min-w-6 place-items-center rounded-full bg-gradient-to-r from-primary to-secondary px-1.5 text-xs font-bold text-white shadow">{{ $cart[$product->id]['qty'] }}</span>
                        @endif
                        <p class="font-semibold text-sm {{ $inCart ? 'text-primary' : 'group-hover:text-primary' }} transition">{{ $product->name }}</p>
                        <p class="text-xs text-text-soft mt-0.5">{{ $product->service?->name }} &middot; per {{ $product->uom ?? 'pc' }}</p>
                        <p class="mt-2 font-display font-bold text-primary">&#8377;{{ number_format($product->priceFor(auth()->user()->branch, $customer), 2) }}</p>
                    </button>
                @empty
                    <p class="col-span-full py-8 text-center text-sm text-text-soft">No products found.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right: cart --}}
    <div class="xl:col-span-2">
        <div class="glass rounded-3xl p-5 xl:sticky xl:top-24" data-reveal>
            <h2 class="font-display text-lg font-semibold">Cart</h2>

            <div class="mt-4 max-h-72 space-y-2 overflow-y-auto pr-1">
                @forelse ($cart as $line)
                    <div class="flex items-center justify-between gap-2 rounded-2xl bg-white/60 px-3 py-2.5 dark:bg-slate-800/60" wire:key="cart-{{ $line['product_id'] }}">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium">{{ $line['name'] }}</p>
                            <p class="text-xs text-text-soft">&#8377;{{ number_format($line['price'], 2) }} each</p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button wire:click="decrement({{ $line['product_id'] }})" class="grid h-7 w-7 place-items-center rounded-lg border border-border text-sm transition hover:border-primary hover:text-primary dark:border-slate-700">&minus;</button>
                            <span class="w-6 text-center text-sm font-semibold">{{ $line['qty'] }}</span>
                            <button wire:click="increment({{ $line['product_id'] }})" class="grid h-7 w-7 place-items-center rounded-lg border border-border text-sm transition hover:border-primary hover:text-primary dark:border-slate-700">+</button>
                        </div>
                        <span class="w-16 text-right text-sm font-semibold">&#8377;{{ number_format($line['price'] * $line['qty'], 0) }}</span>
                        <button wire:click="removeFromCart({{ $line['product_id'] }})" title="Remove"
                                class="grid h-7 w-7 shrink-0 place-items-center rounded-lg text-text-soft transition hover:bg-danger/10 hover:text-danger">
                            <x-icon name="x-mark" class="h-4 w-4" />
                        </button>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-border py-10 text-center text-sm text-text-soft dark:border-slate-700">
                        Tap products to add them here.
                    </div>
                @endforelse
            </div>

            <div class="mt-5 grid grid-cols-2 gap-3">
                <label class="block">
                    <span class="text-xs font-semibold text-text-soft">Discount (&#8377;)</span>
                    <input type="number" min="0" step="0.01" wire:model.live="discount"
                           class="mt-1 w-full rounded-xl border border-border bg-white/70 px-3 py-2 text-sm outline-none focus:border-primary dark:border-slate-700 dark:bg-slate-800/70">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-text-soft">Advance (&#8377;)</span>
                    <input type="number" min="0" step="0.01" wire:model.live="advance"
                           class="mt-1 w-full rounded-xl border border-border bg-white/70 px-3 py-2 text-sm outline-none focus:border-primary dark:border-slate-700 dark:bg-slate-800/70">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-text-soft">Payment method</span>
                    <select wire:model.live="paymentMethod"
                            class="mt-1 w-full rounded-xl border border-border bg-white/70 px-3 py-2 text-sm outline-none focus:border-primary dark:border-slate-700 dark:bg-slate-800/70">
                        <option value="cash">Cash</option>
                        <option value="upi">UPI</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank transfer</option>
                    </select>
                </label>
            </div>

            <div class="mt-3 grid grid-cols-2 gap-3">
                <label class="block">
                    <span class="text-xs font-semibold text-text-soft">Pickup date</span>
                    <input type="date" wire:model="pickupDate" min="{{ now()->toDateString() }}"
                           class="mt-1 w-full rounded-xl border border-border bg-white/70 px-3 py-2 text-sm outline-none focus:border-primary">
                    @error('pickupDate') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-text-soft">Delivery date</span>
                    <input type="date" wire:model="deliveryDate" min="{{ now()->toDateString() }}"
                           class="mt-1 w-full rounded-xl border border-border bg-white/70 px-3 py-2 text-sm outline-none focus:border-primary">
                    @error('deliveryDate') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </label>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-3">
                <label class="block">
                    <span class="text-xs font-semibold text-text-soft">Delivery time</span>
                    <input type="time" wire:model="deliveryTime"
                           class="mt-1 w-full rounded-xl border border-border bg-white/70 px-3 py-2 text-sm outline-none focus:border-primary">
                    @error('deliveryTime') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-text-soft">Delivery address</span>
                    <input type="text" wire:model="deliveryAddress" placeholder="Same as customer address"
                           class="mt-1 w-full rounded-xl border border-border bg-white/70 px-3 py-2 text-sm outline-none focus:border-primary">
                </label>
            </div>
            <label class="mt-3 block">
                <span class="text-xs font-semibold text-text-soft">Notes</span>
                <textarea wire:model="notes" rows="2"
                          class="mt-1 w-full rounded-xl border border-border bg-white/70 px-3 py-2 text-sm outline-none focus:border-primary dark:border-slate-700 dark:bg-slate-800/70"></textarea>
            </label>

            <div class="mt-5 space-y-1.5 rounded-2xl bg-gradient-to-br from-primary/10 to-secondary/10 p-4 text-sm">
                <div class="flex justify-between text-text-soft"><span>Subtotal</span><span>&#8377;{{ number_format($this->subtotal, 2) }}</span></div>
                <div class="flex justify-between text-text-soft"><span>Discount</span><span>&minus;&#8377;{{ number_format((float) $discount, 2) }}</span></div>
                <div class="flex justify-between border-t border-border/60 pt-2 font-display text-base font-bold dark:border-slate-700"><span>Total</span><span>&#8377;{{ number_format($this->total, 2) }}</span></div>
                <div class="flex justify-between text-xs text-text-soft"><span>Advance</span><span>&#8377;{{ number_format((float) $advance, 2) }}</span></div>
                <div class="flex justify-between text-xs font-semibold {{ $this->outstanding > 0 ? 'text-warning' : 'text-success' }}"><span>Outstanding</span><span>&#8377;{{ number_format($this->outstanding, 2) }}</span></div>
            </div>

            @error('customerId')
                <p class="mt-3 rounded-xl bg-danger/10 px-3 py-2 text-xs font-medium text-danger">{{ $message }}</p>
            @enderror
            @error('cart')
                <p class="mt-3 rounded-xl bg-danger/10 px-3 py-2 text-xs font-medium text-danger">{{ $message }}</p>
            @enderror

            <button wire:click="checkout" wire:loading.attr="disabled"
                    class="btn-primary mt-4 w-full justify-center disabled:opacity-50">
                <span wire:loading.remove wire:target="checkout">Create order &amp; invoice</span>
                <span wire:loading wire:target="checkout" class="flex items-center gap-2">
                    <span class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    Processing…
                </span>
            </button>
        </div>
    </div>
</div>