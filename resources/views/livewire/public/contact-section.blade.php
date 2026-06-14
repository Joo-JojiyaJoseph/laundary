<form wire:submit="submit" class="glass space-y-4 rounded-3xl p-7">
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="label">Your name</label>
            <input type="text" wire:model="name" class="input" placeholder="Anjali Menon">
            @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label">Mobile</label>
            <input type="tel" wire:model="phone" class="input" placeholder="98470 12345">
            @error('phone') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
        </div>
    </div>
    <div>
        <label class="label">Email</label>
        <input type="email" wire:model="email" class="input" placeholder="you@example.com">
        @error('email') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label">How can we help?</label>
        <textarea wire:model="message" rows="4" class="input" placeholder="Pickup enquiry, bulk pricing, franchise…"></textarea>
        @error('message') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
    </div>
    <button class="btn-primary w-full justify-center" data-magnetic wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="submit">Send message →</span>
        <span wire:loading wire:target="submit" class="flex items-center gap-2">
            <span class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Sending…
        </span>
    </button>
</form>
