<div>
    <x-admin.page-header title="Items & pricing" subtitle="Garments and articles customers can book, with base prices.">
        <button wire:click="create" class="btn-primary">+ New item</button>
    </x-admin.page-header>

    <div class="glass rounded-3xl p-5" data-reveal>
        <div class="flex flex-wrap gap-3">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search items…" class="input max-w-xs">
            <select wire:model.live="serviceFilter" class="input max-w-44">
                <option value="">All services</option>
                @foreach ($services as $service)
                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="categoryFilter" class="input max-w-44">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="table-admin">
                <thead><tr><th>Item</th><th>Service</th><th>Category</th><th>Base price</th><th>UOM</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr wire:key="prod-{{ $product->id }}">
                            <td class="font-medium">{{ $product->name }}</td>
                            <td class="text-text-soft">{{ $product->service?->name }}</td>
                            <td class="text-text-soft">{{ $product->category?->name ?? '—' }}</td>
                            <td class="font-semibold">₹{{ number_format($product->price, 2) }}</td>
                            <td><span class="badge badge-muted">{{ $product->uom }}</span></td>
                            <td>
                                <button wire:click="toggle({{ $product->id }})" class="badge {{ $product->is_active ? 'badge-success' : 'badge-muted' }}">
                                    {{ $product->is_active ? 'Active' : 'Hidden' }}
                                </button>
                            </td>
                            <td class="text-right space-x-1">
                                <button wire:click="edit({{ $product->id }})" class="btn-soft">Edit</button>
                                <button @click="$dispatch('confirm', { title: 'Delete this?', message: 'Delete this item?', confirmText: 'Yes, delete', method: 'delete', params: [{{ $product->id }}] })" class="btn-danger-soft">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-text-soft">No items found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $products->links() }}</div>
    </div>

    <x-admin.modal show="showModal" :title="$editingId ? 'Edit item' : 'New item'">
        <form wire:submit="save" class="space-y-4">
            {{-- Step 1: Category --}}
            <div>
                <label class="label">1. Category</label>
                <select wire:model.live="form_category_id" class="input">
                    <option value="">Choose a category…</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('form_category_id') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                @if ($categories->isEmpty())
                    <p class="mt-1 text-xs text-warning"><a href="{{ route('admin.categories.index') }}" wire:navigate class="font-semibold underline">Add a category first</a>.</p>
                @endif
            </div>

            {{-- Step 2: Service (filtered by category) --}}
            <div>
                <label class="label">2. Service</label>
                <select wire:model="service_id" class="input" @disabled(! $form_category_id)>
                    <option value="">{{ $form_category_id ? 'Choose a service…' : 'Select a category first' }}</option>
                    @foreach ($formServices as $service)
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
                @error('service_id') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                @if ($form_category_id && $formServices->isEmpty())
                    <p class="mt-1 text-xs text-warning">No services in this category yet — <a href="{{ route('admin.services.index') }}" wire:navigate class="font-semibold underline">add one</a>.</p>
                @endif
            </div>

            {{-- Step 3: Item details --}}
            <div>
                <label class="label">3. Item name</label>
                <input type="text" wire:model="name" class="input" placeholder="Shirt, Saree, Blanket…">
                @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">Base price (₹)</label>
                    <input type="number" step="0.01" wire:model="price" class="input">
                    @error('price') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Unit</label>
                    <select wire:model="uom" class="input">
                        <option value="pc">Per piece</option>
                        <option value="kg">Per kg</option>
                        <option value="pair">Per pair</option>
                        <option value="set">Per set</option>
                        <option value="mtr">Per metre</option>
                    </select>
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model="is_active" class="h-4 w-4 rounded border-border text-primary">
                Available on POS & customer app
            </label>
            <button class="btn-primary w-full justify-center" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save item</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </form>
    </x-admin.modal>
</div>