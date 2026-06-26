<div>
    <x-admin.page-header title="Services" subtitle="Group services under a category. Add items & prices under each service.">
        <button wire:click="create" class="btn-primary">+ New service</button>
    </x-admin.page-header>

    <div class="glass rounded-3xl p-5" data-reveal>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search services…" class="input max-w-xs">

        <div class="mt-4 overflow-x-auto">
            <table class="table-admin table-cards">
                <thead><tr><th>Service</th><th>Category</th><th>Items</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($services as $service)
                        <tr wire:key="svc-{{ $service->id }}">
                            <td class="font-medium">{{ $service->name }}</td>
                            <td class="text-text-soft">{{ $service->category?->name ?: '—' }}</td>
                            <td>
                                <a href="{{ route('admin.products.index', ['serviceFilter' => $service->id]) }}" wire:navigate class="badge badge-primary hover:opacity-80">{{ $service->products_count }} items</a>
                            </td>
                            <td class="text-right space-x-1">
                                <button wire:click="edit({{ $service->id }})" class="btn-soft">Edit</button>
                                <button @click="$dispatch('confirm', { title: 'Delete this service?', message: 'Its items will be hidden too.', confirmText: 'Yes, delete', method: 'delete', params: [{{ $service->id }}] })" class="btn-danger-soft">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-10 text-center text-text-soft">No services yet. Create a category first, then add services under it.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $services->links() }}</div>
    </div>

    <x-admin.modal show="showModal" :title="$editingId ? 'Edit service' : 'New service'">
        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="label">Category</label>
                <select wire:model="product_category_id" class="input">
                    <option value="">— Select category —</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('product_category_id') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                @if ($categories->isEmpty())
                    <p class="mt-1 text-xs text-warning">No categories yet — <a href="{{ route('admin.categories.index') }}" wire:navigate class="font-semibold underline">add one first</a>.</p>
                @endif
            </div>
            <div>
                <label class="label">Service name</label>
                <input type="text" wire:model="name" class="input" placeholder="Dry Cleaning" autofocus>
                @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <button class="btn-primary w-full justify-center" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save service</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </form>
    </x-admin.modal>
</div>
