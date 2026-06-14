<div>
    <x-admin.page-header title="Branches" subtitle="Locations in your laundry network. Each branch has its own staff, pricing and reports.">
        <button wire:click="create" class="btn-primary">+ New branch</button>
    </x-admin.page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" data-reveal>
        @forelse ($branches as $branch)
            <div class="card-float glass rounded-3xl p-5" wire:key="branch-{{ $branch->id }}">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-2xl bg-gradient-to-br from-primary to-secondary text-white"><x-icon name="building-storefront" class="h-5 w-5" /></span>
                        <div>
                            <p class="font-display font-semibold">{{ $branch->name }}</p>
                            <p class="text-xs text-text-soft">{{ $branch->code }}</p>
                        </div>
                    </div>
                    <button wire:click="toggle({{ $branch->id }})" class="badge {{ $branch->is_active ? 'badge-success' : 'badge-muted' }}">
                        {{ $branch->is_active ? 'Open' : 'Closed' }}
                    </button>
                </div>
                <p class="mt-4 text-sm text-text-soft">{{ $branch->address ?: 'No address yet' }}</p>
                <p class="text-xs text-text-soft">{{ $branch->city }} {{ $branch->pincode }}</p>
                <div class="mt-4 flex items-center gap-4 text-sm">
                    <span><strong>{{ $branch->orders_count }}</strong> <span class="text-text-soft">orders</span></span>
                    <span><strong>{{ $branch->customers_count }}</strong> <span class="text-text-soft">customers</span></span>
                </div>
                <div class="mt-4 flex gap-2">
                    <button wire:click="edit({{ $branch->id }})" class="btn-soft flex-1 justify-center">Edit</button>
                    @if ($branch->phone)
                        <a href="tel:{{ $branch->phone }}" class="btn-soft">Call</a>
                    @endif
                </div>
            </div>
        @empty
            <p class="col-span-full py-10 text-center text-text-soft">No branches yet.</p>
        @endforelse
    </div>
    <div class="mt-4">{{ $branches->links() }}</div>

    <x-admin.modal show="showModal" :title="$editingId ? 'Edit branch' : 'New branch'">
        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="label">Branch name</label>
                <input type="text" wire:model="name" class="input" placeholder="Laundrix Kochi HQ">
                @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">Phone</label>
                    <input type="text" wire:model="phone" class="input">
                </div>
                <div>
                    <label class="label">Email</label>
                    <input type="email" wire:model="email" class="input">
                    @error('email') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="label">Address</label>
                <textarea wire:model="address" rows="2" class="input"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">City</label>
                    <input type="text" wire:model="city" class="input">
                </div>
                <div>
                    <label class="label">Pincode</label>
                    <input type="text" wire:model="pincode" class="input">
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model="is_active" class="h-4 w-4 rounded border-border text-primary">
                Branch is operational
            </label>
            <button class="btn-primary w-full justify-center" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save branch</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </form>
    </x-admin.modal>
</div>
