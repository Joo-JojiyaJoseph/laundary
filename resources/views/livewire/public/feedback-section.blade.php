<div>
    <div class="grid items-start gap-12 lg:grid-cols-2">

        {{-- ── Left: existing verified reviews ─────────────────────── --}}
        <div data-reveal>
            <p class="section-eyebrow">Customer feedback</p>
            <h2 class="mt-3 font-display text-4xl font-extrabold sm:text-5xl">What our customers say</h2>

            @if ($reviewCount > 0)
                <div class="mt-4 flex items-center gap-3">
                    <div class="flex">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="h-5 w-5 {{ $i <= round($averageRating) ? 'text-amber-400' : 'text-slate-300' }}"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.36 4.18a1 1 0 00.95.69h4.4c.97 0 1.37 1.24.59 1.81l-3.56 2.59a1 1 0 00-.36 1.12l1.36 4.18c.3.92-.75 1.69-1.54 1.12l-3.56-2.59a1 1 0 00-1.18 0l-3.56 2.59c-.79.57-1.84-.2-1.54-1.12l1.36-4.18a1 1 0 00-.36-1.12L1.4 9.61c-.78-.57-.38-1.81.59-1.81h4.4a1 1 0 00.95-.69L9.05 2.93z" />
                            </svg>
                        @endfor
                    </div>
                    <span class="text-sm font-semibold text-slate-600">{{ number_format($averageRating, 1) }} / 5 ·
                        {{ $reviewCount }} verified {{ Str::plural('review', $reviewCount) }}</span>
                </div>
            @endif

            <p class="mt-4 max-w-md text-slate-500">Every review here is from a real customer — verified with a one-time
                code sent to their mobile. No fakes, no bots.</p>

            <div class="mt-8" wire:loading.remove wire:target="verifyAndSubmit">
                <div x-data="{
                    active: 0,
                    total: {{ count($reviews) }},
                    init() {
                        if (this.total > 1) {
                            setInterval(() => {
                                this.active = (this.active + 1) % this.total;
                            }, 4000);
                        }
                    }
                }" class="relative overflow-hidden">

                    <div class="flex transition-all duration-700 ease-out"
                        :style="'transform: translateX(-' + (active * 100) + '%)'">

                        @forelse ($reviews as $review)
                            <div class="w-full shrink-0 px-2" wire:key="review-{{ $review->id }}">
                                <div
                                    class="group relative overflow-hidden rounded-[32px] bg-white p-8 shadow-xl ring-1 ring-slate-100 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">

                                    <div class="absolute right-6 top-6 text-6xl font-black text-primary/10">
                                        "
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <span
                                            class="grid h-16 w-16 place-items-center rounded-full bg-gradient-to-br from-primary via-secondary to-primary text-lg font-bold text-white shadow-lg">
                                            {{ strtoupper(Str::substr($review->name, 0, 2)) }}
                                        </span>

                                        <div>
                                            <h4 class="text-lg font-bold text-slate-900">
                                                {{ $review->name }}
                                            </h4>

                                            <span
                                                class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600">
                                                ✓ Verified Customer
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-5 flex gap-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="h-5 w-5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200' }}"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.36 4.18a1 1 0 00.95.69h4.4c.97 0 1.37 1.24.59 1.81l-3.56 2.59a1 1 0 00-.36 1.12l1.36 4.18c.3.92-.75 1.69-1.54 1.12l-3.56-2.59a1 1 0 00-1.18 0l-3.56 2.59c-.79.57-1.84-.2-1.54-1.12l1.36-4.18a1 1 0 00-.36-1.12L1.4 9.61c-.78-.57-.38-1.81.59-1.81h4.4a1 1 0 00.95-.69L9.05 2.93z" />
                                            </svg>
                                        @endfor
                                    </div>

                                    <p class="mt-6 text-lg leading-8 text-slate-600">
                                        “{{ $review->message }}”
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="w-full">
                                <div
                                    class="rounded-3xl border border-dashed border-border bg-white/60 p-8 text-center text-slate-500">
                                    Be the first to leave a verified review!
                                </div>
                            </div>
                        @endforelse
                    </div>

                    @if (count($reviews) > 1)
                        <div class="mt-6 flex justify-center gap-2">
                            @foreach ($reviews as $index => $review)
                                <button @click="active={{ $index }}" class="h-3 w-3 rounded-full transition-all"
                                    :class="active === {{ $index }} ?
                                        'bg-primary w-8' :
                                        'bg-slate-300'">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Right: submit feedback (with phone verification) ────── --}}
        <div data-reveal>
            <div class="glass rounded-3xl p-7">
                @if (!$awaitingCode)
                    {{-- Phase 1: write the review --}}
                    <h3 class="font-display text-xl font-bold">Leave your feedback</h3>
                    <p class="mt-1 text-sm text-slate-500">We'll send a quick code to your mobile to verify it's really
                        you.</p>

                    <form wire:submit="sendCode" class="mt-5 space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="label">Your name <span class="text-danger">*</span></label>
                                <input type="text" wire:model.blur="name" class="input" placeholder="Anjali Menon">
                                @error('name')
                                    <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="label">Mobile <span class="text-danger">*</span></label>
                                <input type="tel" wire:model.blur="mobile" class="input" placeholder="98470 12345">
                                @error('mobile')
                                    <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="label">Your rating <span class="text-danger">*</span></label>
                            <div class="mt-1 flex items-center gap-1" x-data="{ hover: 0 }">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" wire:click="$set('rating', {{ $i }})"
                                        @mouseenter="hover = {{ $i }}" @mouseleave="hover = 0"
                                        class="transition hover:scale-110" aria-label="Rate {{ $i }} of 5">
                                        <svg class="h-8 w-8"
                                            :class="(hover ? {{ $i }} <= hover : {{ $i }} <=
                                                {{ $rating }}) ? 'text-amber-400' : 'text-slate-300'"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.36 4.18a1 1 0 00.95.69h4.4c.97 0 1.37 1.24.59 1.81l-3.56 2.59a1 1 0 00-.36 1.12l1.36 4.18c.3.92-.75 1.69-1.54 1.12l-3.56-2.59a1 1 0 00-1.18 0l-3.56 2.59c-.79.57-1.84-.2-1.54-1.12l1.36-4.18a1 1 0 00-.36-1.12L1.4 9.61c-.78-.57-.38-1.81.59-1.81h4.4a1 1 0 00.95-.69L9.05 2.93z" />
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                            @error('rating')
                                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="label">Your feedback <span class="text-danger">*</span></label>
                            <textarea wire:model.blur="message" rows="4" class="input" placeholder="Tell us about your experience…"></textarea>
                            @error('message')
                                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <button class="btn-primary w-full justify-center" wire:loading.attr="disabled"
                            wire:target="sendCode">
                            <span wire:loading.remove wire:target="sendCode">Send verification code →</span>
                            <span wire:loading wire:target="sendCode" class="flex items-center gap-2">
                                <span
                                    class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                                Sending…
                            </span>
                        </button>
                    </form>
                @else
                    {{-- Phase 2: verify the OTP --}}
                    <h3 class="font-display text-xl font-bold">Verify your mobile</h3>
                    <p class="mt-1 text-sm text-slate-500">Enter the 6-digit code we sent to <span
                            class="font-semibold text-slate-700">{{ $mobile }}</span>.</p>

                    <form wire:submit="verifyAndSubmit" class="mt-5 space-y-4">
                        <div>
                            <label class="label">6-digit code <span class="text-danger">*</span></label>
                            <input type="text" inputmode="numeric" maxlength="6" wire:model.blur="code"
                                class="input text-center text-2xl font-bold tracking-[0.5em]" placeholder="••••••">
                            @error('code')
                                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <button class="btn-primary w-full justify-center" wire:loading.attr="disabled"
                            wire:target="verifyAndSubmit">
                            <span wire:loading.remove wire:target="verifyAndSubmit">Verify & post review</span>
                            <span wire:loading wire:target="verifyAndSubmit" class="flex items-center gap-2">
                                <span
                                    class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                                Verifying…
                            </span>
                        </button>

                        <div class="flex items-center justify-between text-sm">
                            <button type="button" wire:click="editDetails"
                                class="font-medium text-slate-500 hover:text-primary">← Edit details</button>
                            <button type="button" wire:click="sendCode"
                                class="font-medium text-primary hover:underline" wire:loading.attr="disabled"
                                wire:target="sendCode">Resend code</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
