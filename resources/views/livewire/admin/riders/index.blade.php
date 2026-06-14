<div>
    <x-admin.page-header title="Riders" subtitle="Pickup & delivery fleet. Locations stream live via Pusher when riders are online.">
        <button wire:click="create" class="btn-primary">+ New rider</button>
    </x-admin.page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" data-reveal>
        @forelse ($riders as $rider)
            <div class="card-float glass rounded-3xl p-5" wire:key="rider-{{ $rider->id }}">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <span class="relative grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-primary to-secondary text-xs font-bold text-white">
                            {{ strtoupper(substr($rider->user->name, 0, 2)) }}
                            <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-white dark:border-slate-900 {{ $rider->is_online ? 'bg-success' : 'bg-slate-400' }}"></span>
                        </span>
                        <div>
                            <p class="font-display font-semibold">{{ $rider->user->name }}</p>
                            <p class="text-xs text-text-soft">{{ $rider->branch?->name ?? 'Unassigned' }}</p>
                        </div>
                    </div>
                    <span class="badge {{ $rider->is_online ? 'badge-success' : 'badge-muted' }}">{{ $rider->is_online ? 'Online' : 'Offline' }}</span>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl bg-white/50 p-3 dark:bg-slate-800/50">
                        <p class="text-xs text-text-soft">Vehicle</p>
                        <p class="font-semibold">{{ $rider->vehicle_number ?: '—' }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/50 p-3 dark:bg-slate-800/50">
                        <p class="text-xs text-text-soft">Deliveries</p>
                        <p class="font-semibold">{{ $rider->orders_count }}</p>
                    </div>
                </div>
                @if ($rider->location_updated_at)
                    <p class="mt-3 text-xs text-text-soft">Last seen {{ $rider->location_updated_at->diffForHumans() }}</p>
                @endif
                <button wire:click="edit({{ $rider->id }})" class="btn-soft mt-4 w-full justify-center">Edit</button>
            </div>
        @empty
            <p class="col-span-full py-10 text-center text-text-soft">No riders yet.</p>
        @endforelse
    </div>
    <div class="mt-4">{{ $riders->links() }}</div>

    <x-admin.modal show="showModal" :title="$editingId ? 'Edit rider' : 'New rider'">
        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="label">Full name</label>
                <input type="text" wire:model="name" class="input">
                @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">Login email</label>
                    <input type="email" wire:model="email" class="input">
                    @error('email') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">{{ $editingId ? 'New password (blank = keep)' : 'Password' }}</label>
                    <input type="password" wire:model="password" class="input">
                    @error('password') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">Vehicle number</label>
                    <input type="text" wire:model="vehicle_number" class="input" placeholder="KL-07-AB-1234">
                </div>
            </div>
            <button class="btn-primary w-full justify-center" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save rider</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </form>
    </x-admin.modal>
</div>
