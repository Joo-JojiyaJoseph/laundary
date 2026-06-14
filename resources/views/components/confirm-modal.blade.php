{{-- Custom confirmation modal — replaces the plain browser confirm().
     From any Livewire view:
       <button @click="$dispatch('confirm', {
           title: 'Delete category?',
           message: 'Items keep working but lose the grouping.',
           confirmText: 'Yes, delete',
           method: 'delete', params: [{{ $category->id }}]
       })">Delete</button>
     On confirm it calls the surrounding Livewire component's method. --}}
<div x-data="{
        show: false, title: '', message: '', confirmText: 'Confirm',
        method: null, params: [], componentEl: null, busy: false,
        open(e) {
            const d = e.detail ?? {};
            this.title = d.title ?? 'Are you sure?';
            this.message = d.message ?? '';
            this.confirmText = d.confirmText ?? 'Confirm';
            this.method = d.method ?? null;
            this.params = d.params ?? [];
            this.componentEl = e.target.closest('[wire\\:id]');
            this.busy = false;
            this.show = true;
        },
        async run() {
            if (!this.method || !this.componentEl || this.busy) return;
            this.busy = true;
            try {
                const component = window.Livewire.find(this.componentEl.getAttribute('wire:id'));
                await component.call(this.method, ...this.params);
            } finally {
                this.show = false;
                this.busy = false;
            }
        }
     }"
     x-on:confirm.window="open($event)"
     x-show="show" x-cloak
     @keydown.escape.window="show = false"
     class="fixed inset-0 z-[65] grid place-items-center p-4">

    <div x-show="show" x-transition.opacity @click="show = false"
         class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>

    <div x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-75 translate-y-6"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-end="opacity-0 scale-90"
         class="glass relative w-full max-w-sm rounded-3xl p-7 text-center">

        <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-gradient-to-br from-warning to-amber-400 shadow-xl shadow-warning/30">
            <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <path d="M12 8v5M12 16.5v.5"/>
            </svg>
        </span>

        <h2 class="mt-4 font-display text-lg font-bold" x-text="title"></h2>
        <p class="mt-1.5 text-sm text-text-soft" x-text="message"></p>

        <div class="mt-6 grid grid-cols-2 gap-3">
            <button @click="show = false"
                    class="rounded-2xl border border-border px-4 py-3 text-sm font-semibold text-text-soft transition hover:border-text-soft hover:text-text">
                Cancel
            </button>
            <button @click="run()" :disabled="busy"
                    class="rounded-2xl bg-gradient-to-r from-danger to-rose-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-danger/25 transition active:scale-95 disabled:opacity-60">
                <span x-show="!busy" x-text="confirmText"></span>
                <span x-show="busy" x-cloak>Working…</span>
            </button>
        </div>
    </div>
</div>
