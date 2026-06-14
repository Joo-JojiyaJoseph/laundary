@props(['show' => 'showModal', 'title' => ''])
<div x-data="{ open: @entangle($show).live }" x-show="open" x-cloak
     class="fixed inset-0 z-50 grid place-items-center p-4">
    <div x-show="open" x-transition.opacity @click="open = false"
         class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-6 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="glass relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-3xl p-6 sm:p-7">
        <div class="mb-5 flex items-center justify-between">
            <h2 class="font-display text-lg font-semibold">{{ $title }}</h2>
            <button @click="open = false" class="grid h-8 w-8 place-items-center rounded-lg text-text-soft transition hover:text-danger">&times;</button>
        </div>
        {{ $slot }}
    </div>
</div>
