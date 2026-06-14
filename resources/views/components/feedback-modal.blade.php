{{-- Global feedback modal. Trigger from any Livewire component:
     $this->dispatch('notify', type: 'success', title: 'Saved!', message: '...');
     $this->dispatch('notify', type: 'error',   title: 'Oops',   message: '...');
     Session flashes `success` / `error` also open it automatically. --}}
<div x-data="{
        show: false, type: 'success', title: '', message: '',
        open(detail) {
            this.type = detail.type ?? 'success';
            this.title = detail.title ?? (this.type === 'success' ? 'Success!' : 'Something went wrong');
            this.message = detail.message ?? '';
            this.show = true;
            if (this.type === 'success') setTimeout(() => this.show = false, 2600);
        }
     }"
     x-on:notify.window="open($event.detail)"
     @if (session('success')) x-init="open({ type: 'success', message: @js(session('success')) })" @endif
     @if (session('error')) x-init="open({ type: 'error', message: @js(session('error')) })" @endif
     x-show="show" x-cloak
     class="fixed inset-0 z-[70] grid place-items-center p-4">

    <div x-show="show" x-transition.opacity @click="show = false"
         class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>

    <div x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-75 translate-y-6"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 scale-90"
         class="glass relative w-full max-w-sm rounded-3xl p-8 text-center">

        {{-- Animated icon --}}
        <template x-if="type === 'success'">
            <span class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-gradient-to-br from-success to-emerald-400 shadow-xl shadow-success/30">
                <svg class="h-10 w-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 13l4 4L19 7">
                        <animate attributeName="stroke-dasharray" from="0 24" to="24 24" dur="0.45s" fill="freeze"/>
                    </path>
                </svg>
            </span>
        </template>
        <template x-if="type === 'error'">
            <span class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-gradient-to-br from-danger to-rose-400 shadow-xl shadow-danger/30">
                <svg class="h-10 w-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
                    <path d="M6 6l12 12M18 6L6 18">
                        <animate attributeName="stroke-dasharray" from="0 34" to="34 34" dur="0.45s" fill="freeze"/>
                    </path>
                </svg>
            </span>
        </template>

        <h2 class="mt-5 font-display text-xl font-bold" x-text="title"></h2>
        <p class="mt-2 text-sm text-text-soft" x-text="message"></p>

        <button @click="show = false"
                class="mt-6 w-full rounded-2xl px-4 py-3 text-sm font-semibold text-white transition active:scale-95"
                :class="type === 'success' ? 'bg-gradient-to-r from-primary to-secondary shadow-lg shadow-primary/25' : 'bg-gradient-to-r from-danger to-rose-500 shadow-lg shadow-danger/25'">
            <span x-text="type === 'success' ? 'Great!' : 'Close'"></span>
        </button>
    </div>
</div>
