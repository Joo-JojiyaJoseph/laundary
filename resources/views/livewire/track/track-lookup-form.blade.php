{{--  <!-- <form wire:submit="track" class="rounded-3xl bg-white/10 p-7 ring-1 ring-white/20 backdrop-blur-xl">
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
</form> -->--}}


<form wire:submit="track"
    class="w-full max-w-2xl bg-white rounded-3xl border border-[#E6E6E6] shadow-sm p-6 sm:p-10 text-left">

    <h3 class="font-serif text-lg mb-6">Track your order</h3>

    {{-- Invoice Number --}}
    <label for="invoice_number"
        class="block text-[10px] tracking-[0.1em] uppercase text-[#6B6B6B] mb-2">
        Invoice Number
    </label>

    <input
        type="text"
        id="invoice_number"
        wire:model="orderNo"
        placeholder="Enter invoice number"
        class="w-full rounded-xl border border-[#E6E6E6] px-4 py-3.5 text-sm placeholder:text-[#A8A8A8] focus:outline-none focus:ring-2 focus:ring-[#E8883E]/40 ">

    @error('orderNo')
        <p class="mt-2 mb-5 text-xs text-red-500">{{ $message }}</p>
    @else
        <div class="mb-5"></div>
    @enderror

    {{-- Mobile Number --}}
    <label for="mobile_number"
        class="block text-[10px] tracking-[0.1em] uppercase text-[#6B6B6B] mb-2">
        Mobile Number
    </label>

    <input
        type="tel"
        id="mobile_number"
        wire:model="mobile"
        placeholder="+91 000 000 0000"
        class="w-full rounded-xl border border-[#E6E6E6] px-4 py-3.5 text-sm placeholder:text-[#A8A8A8] focus:outline-none focus:ring-2 focus:ring-[#E8883E]/40">

    @error('mobile')
        <p class="mt-2 mb-6 text-xs text-red-500">{{ $message }}</p>
    @else
        <div class="mb-6"></div>
    @enderror

    {{-- Submit Button --}}
    <button
        type="submit"
        wire:loading.attr="disabled"
        class="w-full rounded-full bg-[#E8883E] text-white text-sm font-semibold py-4 hover:bg-[#d97a30] transition-colors mb-4 disabled:opacity-70">

        <span wire:loading.remove wire:target="track">
            Track Order
        </span>

        <span wire:loading wire:target="track" class="flex items-center justify-center gap-2">
            <span class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
            Searching...
        </span>
    </button>

    <p class="text-xs text-[#6B6B6B] text-start">
        Need help?
        <a href="#contact"
            class="text-[#1F1F1F] font-medium underline-offset-2 hover:underline">
            Contact support →
        </a>
    </p>

</form>