<div>
    <x-admin.page-header title="Customers" subtitle="Full customer book with loyalty tiers and order history.">
        <button wire:click="create" class="btn-primary">+ New customer</button>
    </x-admin.page-header>

    <div class="glass rounded-3xl p-4 sm:p-5" data-reveal>
        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search name, mobile or code…" class="input sm:max-w-xs">
            <div class="sm:ml-auto">
                <x-admin.date-filter />
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="table-admin table-cards min-w-[720px]">
                <thead><tr><th>Customer</th><th>Contact</th><th>City</th><th>Points</th><th>Orders</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr wire:key="cust-{{ $customer->id }}">
                            <td>
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 place-items-center rounded-full bg-gradient-to-br from-primary to-secondary text-xs font-bold text-white">
                                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                                    </span>
                                    <div>
                                        <p class="font-medium">{{ $customer->name }} @if($customer->is_vip)<span class="badge badge-warning ml-1">VIP</span>@endif</p>
                                        <p class="text-xs text-text-soft">{{ $customer->code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p>{{ $customer->mobile }}</p>
                                <p class="text-xs text-text-soft">{{ $customer->email ?? '—' }}</p>
                            </td>
                            <td class="text-text-soft">{{ $customer->city ?? '—' }}</td>
                            <!-- <td>
                                <span class="badge {{ ['silver' => 'badge-muted', 'gold' => 'badge-warning', 'platinum' => 'badge-primary'][$customer->loyalty_tier] ?? 'badge-muted' }} capitalize">
                                    {{ $customer->loyalty_tier }}
                                </span>
                            </td> -->
                            <td class="font-semibold">{{ number_format($customer->loyalty_points) }}</td>
                            <td><span class="badge badge-primary">{{ $customer->orders_count }}</span></td>
                            <td class="text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn-soft">View</a>
                                <a href="https://wa.me/91{{ preg_replace('/\D/', '', $customer->mobile) }}" target="_blank" class="btn-soft">WhatsApp</a>
                                <button wire:click="edit({{ $customer->id }})" class="btn-soft">Edit</button>
                                <button @click="$dispatch('confirm', { title: 'Delete this?', message: 'Remove this customer? Their orders are kept.', confirmText: 'Yes, delete', method: 'delete', params: [{{ $customer->id }}] })" class="btn-danger-soft">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-text-soft">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $customers->links() }}</div>
    </div>

    <x-admin.modal show="showModal" :title="$editingId ? 'Edit customer' : 'New customer'">
        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="label">Full name <span class="text-danger">*</span></label>
                <input type="text" wire:model.blur="name" class="input" placeholder="e.g. Anita Menon">
                @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">Mobile <span class="text-danger">*</span></label>
                    <input type="text" wire:model.blur="mobile" class="input" placeholder="98765 43210">
                    @error('mobile') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Alternate mobile</label>
                    <input type="text" wire:model.blur="alternate_mobile" class="input">
                    @error('alternate_mobile') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">Email</label>
                    <input type="email" wire:model.blur="email" class="input">
                    @error('email') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Birthday (for rewards)</label>
                    <input type="date" wire:model.blur="birthday" class="input">
                    @error('birthday') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="label">Address <span class="text-danger">*</span></label>
                <textarea wire:model.blur="address" rows="2" class="input" placeholder="House / building, street, area"></textarea>
                @error('address') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="label">City</label>
                    <input type="text" wire:model.blur="city" class="input">
                    @error('city') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Pincode</label>
                    <input type="text" wire:model.blur="pincode" class="input">
                    @error('pincode') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="label">Notes</label>
                <textarea wire:model.blur="notes" rows="2" class="input" placeholder="Preferences, allergies to detergents…"></textarea>
                @error('notes') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <button class="btn-primary w-full justify-center" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save customer</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </form>
    </x-admin.modal>
</div>