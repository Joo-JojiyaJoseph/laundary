<div>
    {{-- Approved reviews --}}
    @if ($reviewCount > 0)
        <div class="mb-12 flex flex-col items-center gap-2 text-center">
            <div class="flex items-center gap-1">
                @for ($i = 1; $i <= 5; $i++)
                    <x-icon name="solid-star" class="h-6 w-6 {{ $i <= round($averageRating) ? 'text-amber-400' : 'text-slate-300' }}" />
                @endfor
            </div>
            <p class="text-sm text-text-soft">
                <span class="font-bold text-text">{{ number_format($averageRating, 1) }}</span> average from
                <span class="font-bold text-text">{{ $reviewCount }}</span> happy {{ \Illuminate\Support\Str::plural('customer', $reviewCount) }}
            </p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($reviews as $review)
                <figure wire:key="rev-{{ $review->id }}" class="glass flex h-full flex-col rounded-3xl p-6">
                    <div class="flex items-center gap-1">
                        @for ($i = 1; $i <= 5; $i++)
                            <x-icon name="solid-star" class="h-4 w-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-300' }}" />
                        @endfor
                    </div>
                    <blockquote class="mt-4 flex-1 text-sm leading-relaxed text-text-soft">“{{ $review->message }}”</blockquote>
                    <figcaption class="mt-5 flex items-center gap-3 border-t border-border/60 pt-4">
                        <span class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-primary to-secondary text-sm font-bold text-white">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($review->name, 0, 1)) }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-text">{{ $review->name }}</p>
                            <p class="text-xs text-text-soft">{{ $review->created_at->format('d M Y') }}</p>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    @else
        <div class="mb-12 text-center text-text-soft">
            <x-icon name="chat-bubble-left-right" class="mx-auto h-10 w-10 text-primary/60" />
            <p class="mt-3 text-sm">Be the first to share your experience with Laundrix.</p>
        </div>
    @endif

    {{-- Submit feedback --}}
    <div class="mx-auto mt-14 max-w-2xl">
        <div class="glass rounded-3xl p-7 sm:p-9">
            <h3 class="font-display text-xl font-bold">Leave us your feedback</h3>
            <p class="mt-1 text-sm text-text-soft">No sign-up needed — your review goes live once our team approves it.</p>

            <form wire:submit="submit" class="mt-6 space-y-5">
                <div>
                    <label class="label">Your name</label>
                    <input type="text" wire:model="name" class="input" placeholder="Anjali Menon">
                    @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="label">Your rating</label>
                    <div class="flex items-center gap-1.5" x-data="{ hover: 0 }">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" wire:click="$set('rating', {{ $i }})"
                                    @mouseenter="hover = {{ $i }}" @mouseleave="hover = 0"
                                    class="transition active:scale-90" aria-label="{{ $i }} star">
                                <x-icon name="solid-star" class="h-8 w-8"
                                        ::class="(hover || {{ $rating }}) >= {{ $i }} ? 'text-amber-400' : 'text-slate-300'" />
                            </button>
                        @endfor
                        <span class="ml-2 text-sm font-semibold text-text-soft">{{ $rating }}/5</span>
                    </div>
                    @error('rating') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="label">Your feedback</label>
                    <textarea wire:model="message" rows="4" class="input" placeholder="Tell us about your experience…"></textarea>
                    @error('message') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <button class="btn-primary w-full justify-center" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submit">Submit feedback →</span>
                    <span wire:loading wire:target="submit" class="flex items-center gap-2">
                        <span class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Submitting…
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>
