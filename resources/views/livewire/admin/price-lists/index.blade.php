<div>
    <x-admin.page-header title="Price lists" subtitle="Branch, customer, VIP, seasonal and promotional overrides. Resolution order: customer → VIP → seasonal/promo → branch → base.">
        <button wire:click="create" class="btn-primary">+ New rule</button>
    </x-admin.page-header>

    <div class="glass rounded-3xl p-5" data-reveal>
        <div class="flex flex-wrap gap-2">
            @foreach (['' => 'All', 'branch' => 'Branch', 'customer' => 'Customer', 'vip' => 'VIP', 'seasonal' => 'Seasonal', 'promo' => 'Promo'] as $value => $labelText)
                <button wire:click="$set('typeFilter', '{{ $value }}')"
                        class="rounded-full px-4 py-1.5 text-xs font-semibold transition
                               {{ $typeFilter === $value ? 'bg-gradient-to-r from-primary to-secondary text-white shadow-md shadow-primary/25' : 'border border-border text-text-soft hover:border-primary hover:text-primary dark:border-slate-700' }}">
                    {{ $labelText }}
                </button>
            @endforeach
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="table-admin">
                <thead><tr><th>Item</th><th>Type</th><th>Scope</th><th>Price</th><th>Window</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($rules as $rule)
                        <tr wire:key="rule-{{ $rule->id }}">
                            <td>
                                <p class="font-medium">{{ $rule->product?->name }}</p>
                                <p class="text-xs text-text-soft">{{ $rule->product?->service?->name }} · base ₹{{ number_format($rule->product?->price ?? 0, 0) }}</p>
                            </td>
                            <td><span class="badge badge-primary capitalize">{{ $rule->type }}</span></td>
                            <td class="text-text-soft text-xs">
                                {{ $rule->customer?->name ?? $rule->branch?->name ?? 'All branches' }}
                            </td>
                            <td class="font-semibold">₹{{ number_format($rule->price, 2) }}</td>
                            <td class="text-xs text-text-soft">
                                {{ $rule->starts_at?->format('d M y') ?? '∞' }} → {{ $rule->ends_at?->format('d M y') ?? '∞' }}
                            </td>
                            <td>
                                <button wire:click="toggle({{ $rule->id }})" class="badge {{ $rule->is_active ? 'badge-success' : 'badge-muted' }}">
                                    {{ $rule->is_active ? 'Active' : 'Paused' }}
                                </button>
                            </td>
                            <td class="text-right space-x-1">
                                <button wire:click="edit({{ $rule->id }})" class="btn-soft">Edit</button>
                                <button @click="$dispatch('confirm', { title: 'Delete this?', message: 'Delete this price rule?', confirmText: 'Yes, delete', method: 'delete', params: [{{ $rule->id }}] })" class="btn-danger-soft">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-text-soft">No price rules yet — items use their base price.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $rules->links() }}</div>
    </div>

    <x-admin.modal show="showModal" :title="$editingId ? 'Edit price rule' : 'New price rule'">
        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="label">Item</label>
                <select wire:model="product_id" class="input">
                    <option value="">Choose item…</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} — {{ $product->service?->name }} (₹{{ number_format($product->price, 0) }})</option>
                    @endforeach
                </select>
                @error('product_id') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">Rule type</label>
                    <select wire:model.live="type" class="input">
                        <option value="branch">Branch price</option>
                        <option value="customer">Customer price</option>
                        <option value="vip">VIP price</option>
                        <option value="seasonal">Seasonal</option>
                        <option value="promo">Promotional</option>
                    </select>
                </div>
                <div>
                    <label class="label">Override price (₹)</label>
                    <input type="number" step="0.01" wire:model="price" class="input">
                    @error('price') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            @if (in_array($type, ['branch', 'vip', 'seasonal', 'promo']))
                <div>
                    <label class="label">Branch {{ $type === 'branch' ? '' : '(optional — blank applies everywhere)' }}</label>
                    <select wire:model="branch_id" class="input">
                        <option value="">{{ $type === 'branch' ? 'Choose branch…' : 'All branches' }}</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            @endif

            @if ($type === 'customer')
                <div>
                    <label class="label">Customer</label>
                    <input type="search" wire:model.live.debounce.300ms="customerSearch" class="input" placeholder="Search name or mobile…">
                    @if ($customers->isNotEmpty())
                        <div class="mt-2 space-y-1">
                            @foreach ($customers as $found)
                                <button type="button" wire:click="$set('customer_id', {{ $found->id }})"
                                        class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-sm transition
                                               {{ $customer_id === $found->id ? 'bg-gradient-to-r from-primary/15 to-secondary/10 font-semibold' : 'hover:bg-primary/5' }}">
                                    <span>{{ $found->name }}</span>
                                    <span class="text-xs text-text-soft">{{ $found->mobile }}</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                    @error('customer_id') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            @endif

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">Starts (optional)</label>
                    <input type="date" wire:model="starts_at" class="input">
                </div>
                <div>
                    <label class="label">Ends (optional)</label>
                    <input type="date" wire:model="ends_at" class="input">
                    @error('ends_at') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model="is_active" class="h-4 w-4 rounded border-border text-primary">
                Rule is active
            </label>
            <button class="btn-primary w-full justify-center" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save rule</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </form>
    </x-admin.modal>
</div>
