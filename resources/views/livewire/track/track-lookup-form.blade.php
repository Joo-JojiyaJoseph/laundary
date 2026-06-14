<form wire:submit="track" class="rounded-3xl bg-white/10 p-7 ring-1 ring-white/20 backdrop-blur-xl">
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-semibold text-white">Order number</label>
            <input type="text" wire:model="orderNo"
                   class="w-full rounded-xl border border-white/25 bg-white/95 px-4 py-3 text-sm font-medium uppercase text-slate-900 placeholder:text-slate-400 outline-none focus:border-white focus:ring-2 focus:ring-white/40"
                   placeholder="LDS00123">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-semibold text-white">Registered mobile</label>
            <input type="tel" wire:model="mobile"
                   class="w-full rounded-xl border border-white/25 bg-white/95 px-4 py-3 text-sm font-medium text-slate-900 placeholder:text-slate-400 outline-none focus:border-white focus:ring-2 focus:ring-white/40"
                   placeholder="98470 12345">
            @error('mobile') <p class="mt-1 text-xs font-medium text-amber-300">{{ $message }}</p> @enderror
        </div>
    </div>
    @error('orderNo') <p class="mt-3 text-sm font-medium text-amber-300">{{ $message }}</p> @enderror
    <button class="btn-primary mt-5 w-full justify-center !rounded-xl !py-3.5" data-magnetic wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="track">Track my order →</span>
        <span wire:loading wire:target="track" class="flex items-center gap-2">
            <span class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Searching…
        </span>
    </button>
    <p class="mt-4 text-center text-xs text-sky-100/70">Tip: scanning the QR code on your invoice opens tracking instantly — no typing.</p>
</form>
