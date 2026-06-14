<div>
    <x-admin.page-header title="Product categories" subtitle="Group garments and items for the catalogue & POS.">
        <button wire:click="create" class="btn-primary">+ New category</button>
    </x-admin.page-header>

    <div class="glass rounded-3xl p-5" data-reveal>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search categories…" class="input max-w-xs">

        <div class="mt-4 overflow-x-auto">
            <table class="table-admin">
                <thead><tr><th>Name</th><th>Items</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr wire:key="cat-{{ $category->id }}">
                            <td class="font-medium">{{ $category->name }}</td>
                            <td><span class="badge badge-primary">{{ $category->products_count }}</span></td>
                            <td class="text-right space-x-1">
                                <button wire:click="edit({{ $category->id }})" class="btn-soft">Edit</button>
                                <button @click="$dispatch('confirm', { title: 'Delete this?', message: 'Delete this category? Items keep working but lose the grouping.', confirmText: 'Yes, delete', method: 'delete', params: [{{ $category->id }}] })" class="btn-danger-soft">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-10 text-center text-text-soft">No categories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $categories->links() }}</div>
    </div>

    <x-admin.modal show="showModal" :title="$editingId ? 'Edit category' : 'New category'">
        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="label">Name</label>
                <input type="text" wire:model="name" class="input" placeholder="e.g. Men's Wear">
                @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <button class="btn-primary w-full justify-center" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save category</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </form>
    </x-admin.modal>
</div>
