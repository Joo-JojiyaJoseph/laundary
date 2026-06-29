<div class="bg-[#FAFAF8] text-[#1F1F1F] antialiased ">

    {{-- ============================================================ --}}
    {{-- NAVIGATION --}}
    {{-- ============================================================ --}}
    <header class="absolute top-0 left-0 right-0 z-30 hidden">
        <nav class="max-w-[1920px] mx-auto flex items-center justify-between px-6 sm:px-10 lg:px-[120px] py-6" aria-label="Primary navigation">
            <a href="{{ url('/') }}" class="font-serif text-2xl tracking-tight text-white">Laundrix</a>

            <ul class="hidden lg:flex items-center gap-10 text-sm font-medium text-white/90">
                <li><a href="#home" class="hover:text-white transition-colors">Home</a></li>
                <li><a href="#about" class="hover:text-white transition-colors">About</a></li>
                <li><a href="#services" class="hover:text-white transition-colors">Services</a></li>
                <li><a href="#track-order" class="hover:text-white transition-colors">Track Order</a></li>
                <li><a href="#reviews" class="hover:text-white transition-colors">Reviews</a></li>
                <li><a href="#contact" class="hover:text-white transition-colors">Contact</a></li>
            </ul>

            <a href="#sign-in"
                class="hidden sm:inline-flex items-center justify-center rounded-full border border-white/70 text-white text-sm font-medium px-5 py-2 hover:bg-white hover:text-[#1F1F1F] transition-colors">
                Sign In
            </a>

            {{-- Mobile menu button (static markup only — no JS per spec) --}}
            <button type="button" class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-full border border-white/70 text-white" aria-label="Open menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6" />
                    <line x1="3" y1="12" x2="21" y2="12" />
                    <line x1="3" y1="18" x2="21" y2="18" />
                </svg>
            </button>
        </nav>
    </header>

    {{-- ============================================================ --}}
    {{-- HERO --}}
    {{-- ============================================================ --}}
    <section id="home" class="relative ">
        <div class="relative min-h-screen overflow-hidden">
            <img src="/images/home/ded64d6282a3c67edffa1fcd2a1e90f03dc47e4a.jpg"
                alt="Freshly folded towels in warm afternoon light"
                class="absolute inset-0 w-full h-full object-cover -scale-x-100">
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>

            <div class="relative min-h-screen w-full px-6 md:px-10 lg:px-[80px] flex flex-col justify-center container mx-auto">
                <div class="w-full lg:mt-28">
                    <p class="text-[#E8883E] text-xs sm:text-sm tracking-[0.20em] uppercase mb-4">
                        Kerala's Modern Laundry Network
                    </p>
                    <h1 class="font-serif text-white text-4xl lg:text-5xl xl:text-6xl leading-16 mb-6">
                        Quality Laundry<br>Every Thread
                    </h1>
                    <p class="text-white text-sm leading-relaxed mb-8 font-light font-sans">
                        Kerala's modern laundry dry-cleaning network. We pick up, wash, <br class="hidden lg:flex"> steam-iron and deliver every garment back to your door — and you can<br class="hidden lg:flex"> follow it live, every step of the way.
                    </p>
                    <div class="flex flex-wrap items-center gap-4">
                        <a href="#about"
                            class="inline-flex items-center justify-center rounded-full bg-[#E8883E] text-white text-sm ] px-7 py-4 hover:bg-[#d97a30] transition-colors">
                            Discover More
                        </a>
                        <a href="#contact"
                            class="inline-flex items-center justify-center rounded-full border-2 border-white/70 text-white text-sm ] px-7 py-4 hover:bg-white hover:text-[#1F1F1F] transition-colors">
                            Contact Us
                        </a>
                    </div>
                </div>

                {{-- In-hero glance stats — translucent capsules, in normal flow --}}
                <div class="pt-16">
                    <div class="grid grid-cols-3 gap-3 sm:gap-4 text-white w-full">
                        <div class="w-full border border-white/20 bg-white/[0.08] p-4 rounded-2xl backdrop-blur-2xl">
                            <p class="text-[10px] tracking-[0.15em] uppercase text-white/60 mb-1">Garments / Month</p>
                            <p class="font-serif text-xl sm:text-2xl">48,000+</p>
                        </div>
                        <div class="w-full border border-white/20 bg-white/[0.08] p-4 rounded-2xl backdrop-blur-2xl">
                            <p class="text-[10px] tracking-[0.15em] uppercase text-white/60 mb-1">Partner Stores</p>
                            <p class="font-serif text-xl sm:text-2xl">120+</p>
                        </div>
                        <div class="w-full border border-white/20 bg-white/[0.08] p-4 rounded-2xl backdrop-blur-2xl">
                            <p class="text-[10px] tracking-[0.15em] uppercase text-white/60 mb-1">On-Time Delivery</p>
                            <p class="font-serif text-xl sm:text-2xl">98%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- STAT CARDS --}}
    {{-- ============================================================ --}}
    <section class="bg-[#FAFAF8] py-16 container mx-auto px-6 md:px-10 lg:px-[80px]">
        <div class="">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">

                <!-- Card 1 -->
                <div class="bg-white border border-[#E6E6E6] p-6 rounded-2xl flex flex-col justify-center">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-6 h-[2px] bg-[#E8883E]"></span>
                        <span class="text-[11px] uppercase tracking-[0.22em] text-[#E8883E] font-semibold">
                            Monthly
                        </span>
                    </div>

                    <h3 class="font-serif text-lg lg:text-[44px] leading-none text-[#2B2B2B]">
                        48,000+
                    </h3>

                    <p class="mt-5 text-[15px] leading-7 text-[#787878]">
                        Garments processed with precision every month.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="bg-white border border-[#E6E6E6] p-6 rounded-2xl flex flex-col justify-center">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-6 h-[2px] bg-[#E8883E]"></span>
                        <span class="text-[11px] uppercase tracking-[0.22em] text-[#E8883E] font-semibold">
                            Network
                        </span>
                    </div>

                    <h3 class="font-serif text-lg lg:text-[44px] leading-none text-[#2B2B2B]">
                        120+
                    </h3>

                    <p class="mt-5 text-[15px] leading-7 text-[#787878]">
                        Partner stores across Kerala, unified by one standard.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="bg-white border border-[#E6E6E6] p-6 rounded-2xl flex flex-col justify-center">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-6 h-[2px] bg-[#E8883E]"></span>
                        <span class="text-[11px] uppercase tracking-[0.22em] text-[#E8883E] font-semibold">
                            Delivery
                        </span>
                    </div>

                    <h3 class="font-serif text-lg lg:text-[44px] leading-none text-[#2B2B2B]">
                        98%
                    </h3>

                    <p class="mt-5 text-[15px] leading-7 text-[#787878]">
                        On-time delivery rate built for your schedule.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- WHY LAUNDRIX --}}
    {{-- ============================================================ --}}
    <section class="bg-[#F3F2EF]">
        <div class="py-16 lg:py-24 container mx-auto px-6 md:px-10 lg:px-[80px]">
            <p class="text-[#E8883E] text-xs font-semibold tracking-[0.15em] uppercase mb-3">Why Laundrix</p>
            <h2 class="font-serif text-3xl sm:text-4xl mb-12">Designed for the way you live.</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 lg:gap-8">
                {{-- Card 1 --}}
                <div class="bg-white rounded-2xl border border-[#E6E6E6] p-6">
                    <div class="w-14 h-14 rounded-full bg-[#F3F2EF] flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#E8883E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M8.5 14s1.5 2 3.5 2 3.5-2 3.5-2" />
                            <line x1="9" y1="9" x2="9.5" y2="9" />
                            <line x1="15" y1="9" x2="15.5" y2="9" />
                        </svg>
                    </div>
                    <h3 class="font-serif text-lg mb-2">100% Happiness Guarantee</h3>
                    <p class="text-sm text-[#6B6B6B] leading-relaxed">If it's not perfect, we redo it-no questions asked.</p>
                </div>

                {{-- Card 2 --}}
                <div class="bg-white rounded-2xl border border-[#E6E6E6] p-6">
                    <div class="w-14 h-14 rounded-full bg-[#F3F2EF] flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#E8883E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="9" width="13" height="8" rx="1" />
                            <path d="M15 12h3l3 3v2h-6z" />
                            <circle cx="6.5" cy="19" r="1.6" />
                            <circle cx="17" cy="19" r="1.6" />
                        </svg>
                    </div>
                    <h3 class="font-serif text-lg mb-2">Free Collection &amp; Delivery</h3>
                    <p class="text-sm text-[#6B6B6B] leading-relaxed">Door-to-door service that fits your schedule.</p>
                </div>

                {{-- Card 3 --}}
                <div class="bg-white rounded-2xl border border-[#E6E6E6] p-6">
                    <div class="w-14 h-14 rounded-full bg-[#F3F2EF] flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#E8883E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 13a8 8 0 0 1 16 0" />
                            <path d="M4 13v4a2 2 0 0 0 2 2h1v-7H5a1 1 0 0 0-1 1z" />
                            <path d="M20 13v4a2 2 0 0 1-2 2h-1v-7h1a1 1 0 0 1 1 1z" />
                        </svg>
                    </div>
                    <h3 class="font-serif text-lg mb-2">24/7 Dedicated Support</h3>
                    <p class="text-sm text-[#6B6B6B] leading-relaxed">Help whenever you need it-no hold music, no hassle.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- OUR STORY --}}
    {{-- ============================================================ --}}
    <section id="about" class="bg-[#FAFAF8] container mx-auto px-6 md:px-10 lg:px-[80px]">
        <div class="py-16 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                {{-- Text --}}
                <div>
                    <p class="text-[#E8883E] text-xs font-semibold tracking-[0.15em] uppercase mb-3">Our Story</p>
                    <h2 class="font-serif text-3xl sm:text-4xl leading-tight mb-6">Experience The Pinnacle Of Laundry Excellence</h2>
                    <p class="text-sm sm:text-base text-[#6B6B6B] leading-relaxed mb-8">
                        From everyday wash &amp; fold to delicate kasavu sarees, we handle every fabric with eco-friendly chemistry, garment-level tagging and live tracking — built around the rhythm of Kerala's monsoons and festivals.
                    </p>
                    <a href="#services"
                        class="inline-flex items-center justify-center rounded-full bg-[#E8883E] text-white text-sm font-semibold px-8 py-3 hover:bg-[#d97a30] transition-colors">
                        Learn more
                    </a>
                </div>

                {{-- Image --}}
                <div class="w-full aspect-[808/560] rounded-3xl overflow-hidden">
                    <img src="/images/home/about-image.svg" alt="Laundrix team caring for freshly folded garments" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- OUR SERVICES --}}
    {{-- ============================================================ --}}
    <section id="services" class="bg-[#F3F2EF] ">
        <div class="py-16 container mx-auto px-6 md:px-10 lg:px-[80px]">
            <p class="text-[#E8883E] text-xs font-semibold tracking-[0.15em] uppercase mb-3">Our Services</p>
            <h2 class="font-serif text-3xl sm:text-4xl mb-12">Care tailored to every fabric.</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                @php
                $services = [
                ['title' => 'Dry Cleaning', 'img' => '/images/home/image 4.svg'],
                ['title' => 'Wash & Fold', 'img' => '/images/home/image.svg'],
                ['title' => 'Premium Laundry', 'img' => '/images/home/image (1).svg'],
                ['title' => 'Steam Ironing', 'img' => '/images/home/image (2).svg'],
                ['title' => 'Shoe Cleaning', 'img' => '/images/home/image (3).svg'],
                ['title' => 'Curtain Cleaning', 'img' => '/images/home/image (4).svg'],
                ];
                @endphp

                @foreach ($services as $service)
                <article class="bg-white rounded-2xl border border-[#E6E6E6] p-5">
                    <img src="{{ $service['img'] }}" alt="{{ $service['title'] }} service" class="w-full h-44 sm:h-48 lg:h-[220px] object-cover rounded-xl mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-serif text-lg">{{ $service['title'] }}</h3>
                        <a href="#contact" aria-label="Learn more about {{ $service['title'] }}" class="text-[#E8883E]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                        </a>
                    </div>
                    <p class="text-sm text-[#6B6B6B] leading-relaxed">
                        Professional {{ $service['title'] }} with eco-friendly detergents, garment-level tagging and live tracking.
                    </p>
                </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- HOW IT WORKS --}}
    {{-- ============================================================ --}}
    <section class="bg-[#FAFAF8] container mx-auto px-6 md:px-10 lg:px-[80px]">
        <div class=" py-16 lg:py-24">
            <p class="text-[#E8883E] text-xs font-semibold tracking-[0.15em] uppercase mb-3">How It Works</p>
            <h2 class="font-serif text-3xl sm:text-4xl mb-14">Simple, seamless, and completely transparent.</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                {{-- Step 1 --}}
                <div>
                    <div class="flex items-center mb-5">
                        <div class="w-10 h-10 shrink-0 rounded-full bg-[#E8883E] text-white text-sm font-semibold flex items-center justify-center">01</div>
                        <div class="hidden sm:block flex-1 h-px bg-[#E6E6E6] ml-4"></div>
                    </div>
                    <h3 class="font-serif text-lg mb-2">Schedule Pickup</h3>
                    <p class="text-sm text-[#6B6B6B] leading-relaxed">Book online or in-app. We'll collect your garments at a time that works for you.</p>
                </div>
                {{-- Step 2 --}}
                <div>
                    <div class="flex items-center mb-5">
                        <div class="w-10 h-10 shrink-0 rounded-full bg-[#E8883E] text-white text-sm font-semibold flex items-center justify-center">02</div>
                        <div class="hidden sm:block flex-1 h-px bg-[#E6E6E6] ml-4"></div>
                    </div>
                    <h3 class="font-serif text-lg mb-2">Professional Cleaning</h3>
                    <p class="text-sm text-[#6B6B6B] leading-relaxed">Our specialists assess, clean, and finish every item with precision and care.</p>
                </div>
                {{-- Step 3 --}}
                <div>
                    <div class="flex items-center mb-5">
                        <div class="w-10 h-10 shrink-0 rounded-full bg-[#E8883E] text-white text-sm font-semibold flex items-center justify-center">03</div>
                    </div>
                    <h3 class="font-serif text-lg mb-2">Delivered To You</h3>
                    <p class="text-sm text-[#6B6B6B] leading-relaxed">Your order is hand-delivered, folded, and ready to wear.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="feedback" class="bg-[#FAFAF8]">
        <div class="container mx-auto px-6 md:px-10 lg:px-[80px] py-16 lg:py-24">

            <div data-reveal class="flex flex-col items-center text-center">
                <p class="text-[#E8883E] text-xs font-semibold tracking-[0.15em] uppercase mb-3">Customer Feedback</p>
                <h2 class="font-serif text-3xl sm:text-4xl mb-4">What our customers say.</h2>
                <p class="text-sm text-[#6B6B6B] max-w-xl mb-12">Real reviews from real people — and we'd love to hear about your experience too.</p>
            </div>

            {{-- Approved reviews --}}
            @if ($reviewCount > 0)
            <div class="mb-10 flex flex-col items-center gap-2 text-center">
                <div class="flex items-center gap-1">
                    @for ($i = 1; $i
                    <= 5; $i++)
                        <x-icon name="solid-star" class="h-5 w-5 {{ $i <= round($averageRating) ? 'text-[#C5A059]' : 'text-[#E6E6E6]' }}" />
                    @endfor
                </div>
                <p class="text-sm text-[#6B6B6B]">
                    <span class="font-semibold text-[#1F1F1F]">{{ number_format($averageRating, 1) }}</span> average from
                    <span class="font-semibold text-[#1F1F1F]">{{ $reviewCount }}</span> happy {{ \Illuminate\Support\Str::plural('customer', $reviewCount) }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 mb-14">
                @foreach ($reviews as $review)
                <figure wire:key="rev-{{ $review->id }}" class="bg-white rounded-2xl border border-[#E6E6E6] p-7 flex h-full flex-col">
                    <div class="flex items-center gap-1">
                        @for ($i = 1; $i
                        <= 5; $i++)
                            <x-icon name="solid-star" class="h-3.5 w-3.5 {{ $i <= $review->rating ? 'text-[#C5A059]' : 'text-[#E6E6E6]' }}" />
                        @endfor
                    </div>
                    <blockquote class="mt-4 flex-1 text-sm leading-relaxed text-[#3A3A3A]">"{{ $review->message }}"</blockquote>
                    <figcaption class="mt-5 flex items-center gap-3 border-t border-[#E6E6E6] pt-4">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#E8883E] text-sm font-semibold text-white">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($review->name, 0, 1)) }}
                        </span>
                        <div>
                            <p class="text-sm font-medium text-[#1F1F1F]">{{ $review->name }}</p>
                            <p class="text-xs text-[#6B6B6B]">{{ $review->created_at->format('d M Y') }}</p>
                        </div>
                    </figcaption>
                </figure>
                @endforeach
            </div>
            @else
            <div class="mb-14 text-center text-[#6B6B6B]">
                <x-icon name="chat-bubble-left-right" class="mx-auto h-10 w-10 text-[#E8883E]/60" />
                <p class="mt-3 text-sm">Be the first to share your experience with Laundrix.</p>
            </div>
            @endif

            {{-- Submit feedback --}}
            <div class="mx-auto max-w-2xl">
                <div class="bg-white rounded-3xl border border-[#E6E6E6] shadow-sm p-6 sm:p-10">
                    <h3 class="font-serif text-lg mb-1">Leave us your feedback</h3>
                    <p class="text-sm text-[#6B6B6B] mb-6">No sign-up needed — your review goes live once our team approves it.</p>

                    <form wire:submit="ratingSubmit" class="space-y-5">
                        <div>
                            <label class="block text-[10px] tracking-[0.1em] uppercase text-[#6B6B6B] mb-2">Your name</label>
                            <input type="text" wire:model="ratingName" placeholder="Anjali Menon"
                                class="w-full rounded-xl border border-[#E6E6E6] px-4 py-3.5 text-sm placeholder:text-[#A8A8A8] focus:outline-none focus:ring-2 focus:ring-[#E8883E]/40">
                            @error('ratingName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] tracking-[0.1em] uppercase text-[#6B6B6B] mb-2">Your rating</label>
                            <div class="flex items-center gap-1.5" x-data="{ hover: 0 }">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" wire:click="$set('rating', {{ $i }})"
                                    @mouseenter="hover = {{ $i }}" @mouseleave="hover = 0"
                                    class="transition active:scale-90" aria-label="{{ $i }} star">
                                    <x-icon name="solid-star" class="h-8 w-8"
                                        ::class="(hover || {{ $rating }}) >= {{ $i }} ? 'text-[#C5A059]' : 'text-[#E6E6E6]'" />
                                    </button>
                                    @endfor
                                    <span class="ml-2 text-sm font-medium text-[#6B6B6B]">{{ $rating }}/5</span>
                            </div>
                            @error('rating') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] tracking-[0.1em] uppercase text-[#6B6B6B] mb-2">Your feedback</label>
                            <textarea wire:model="ratingMessage" rows="4" placeholder="Tell us about your experience…"
                                class="w-full rounded-xl border border-[#E6E6E6] px-4 py-3.5 text-sm placeholder:text-[#A8A8A8] focus:outline-none focus:ring-2 focus:ring-[#E8883E]/40"></textarea>
                            @error('ratingMessage') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <button class="w-full inline-flex items-center justify-center rounded-full bg-[#E8883E] text-white text-sm font-semibold py-4 hover:bg-[#d97a30] transition-colors" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submit">Submit feedback →</span>
                            <span wire:loading wire:target="submit" class="flex items-center gap-2">
                                <span class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Submitting…
                            </span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- TRACK ORDER --}}
    {{-- ============================================================ --}}
    <section id="track-order" class="bg-[#F3F2EF]">
        <div class="container mx-auto px-6 md:px-10 lg:px-[80px] py-16 lg:py-24 flex flex-col items-center text-center">
            <p class="text-[#E8883E] text-xs font-semibold tracking-[0.15em] uppercase mb-3">Track Your Order</p>
            <h2 class="font-serif text-3xl sm:text-4xl mb-10">Where is your order?</h2>

            {{--
            <form class="w-full max-w-2xl bg-white rounded-3xl border border-[#E6E6E6] shadow-sm p-6 sm:p-10 text-left">
                <h3 class="font-serif text-lg mb-6">Track your order</h3>

                <label for="invoice_number" class="block text-[10px] tracking-[0.1em] uppercase text-[#6B6B6B] mb-2">Invoice Number</label>
                <input type="text" id="invoice_number" name="invoice_number" placeholder="Enter invoice number"
                    class="w-full rounded-xl border border-[#E6E6E6] px-4 py-3.5 text-sm mb-5 placeholder:text-[#A8A8A8] focus:outline-none focus:ring-2 focus:ring-[#E8883E]/40">

                <label for="mobile_number" class="block text-[10px] tracking-[0.1em] uppercase text-[#6B6B6B] mb-2">Mobile Number</label>
                <input type="tel" id="mobile_number" name="mobile_number" placeholder="+91 000 000 0000"
                    class="w-full rounded-xl border border-[#E6E6E6] px-4 py-3.5 text-sm mb-6 placeholder:text-[#A8A8A8] focus:outline-none focus:ring-2 focus:ring-[#E8883E]/40">

                <button type="submit"
                    class="w-full rounded-full bg-[#E8883E] text-white text-sm font-semibold py-4 hover:bg-[#d97a30] transition-colors mb-4">
                    Track Order
                </button>

                <p class="text-xs text-[#6B6B6B] text-start">
                    Need help? <a href="#contact" class="text-[#1F1F1F] font-medium underline-offset-2 hover:underline">Contact support →</a>
                </p>
            </form>  --}}
             <livewire:track.track-lookup-form />
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- CTA BANNER --}}
    {{-- ============================================================ --}}
    <section class="relative ">
        <div class="relative h-[360px] lg:h-[640px] overflow-hidden">
            <img src="/images/home/000edd5bad91fae89ec9bd0682080f3a7a9437e3.png"
                alt="Stack of folded towels" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/65"></div>

            <div class="lg:mt-10 relative h-full px-6 flex flex-col items-center justify-center lg:justify-start text-center lg:py-20">
                <h2 class="font-serif text-white text-3xl sm:text-4xl lg:text-6xl leading-tight mb-4">Your Clothes Deserve Better <br class="hidden lg:flex"> Care.</h2>
                <p class="text-white/80 text-sm mb-8">
                    Experience Kerala's modern laundry network-precision, convenience, and a finish <br class="hidden lg:flex">that feels premium.
                </p>
                <a href="#track-order"
                    class="lg:mt-15 xl:mt-20 inline-flex items-center justify-center rounded-full bg-[#E8883E] text-white text-sm font-semibold px-7 py-4 hover:bg-[#d97a30] transition-colors">
                    Book Free Pickup
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- TESTIMONIALS --}}
    {{-- ============================================================ --}}
    <section id="reviews" class="bg-[#FAFAF8]">
        <div class="container mx-auto px-6 md:px-10 lg:px-[80px] py-16 lg:py-24">
            <p class="text-[#E8883E] text-xs font-semibold tracking-[0.15em] uppercase mb-3">What Our Customers Say</p>
            <h2 class="font-serif text-3xl sm:text-4xl mb-12">Consistently exceptional.</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 lg:gap-8 mb-10">
                @php
                $testimonials = [
                ['name' => 'Aisha N.', 'text' => "The attention to detail is incredible. My wool coats look brand new, and delivery is always on time."],
                ['name' => 'Rohan M.', 'text' => "The best laundry experience in Kerala. The app is clean, the service is reliable, and the quality is consistently high."],
                ['name' => 'Meera S.', 'text' => "The steam ironing is flawless. I use Laundrix for everything from everyday shirts to special occasion dresses."],
                ];
                @endphp

                @foreach ($testimonials as $t)
                <article class="bg-white rounded-2xl border border-[#E6E6E6] p-7">
                    <span class="font-serif text-3xl text-[#E8883E] leading-none">"</span>
                    <p class="text-sm text-[#3A3A3A] leading-relaxed mt-2 mb-6">{{ $t['text'] }}</p>
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium">{{ $t['name'] }}</p>
                        <div class="flex gap-0.5 text-[#C5A059]" aria-label="5 out of 5 stars">
                            @for ($i = 0; $i < 5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l2.9 6.6 7.1.6-5.4 4.7 1.7 7-6.3-3.8-6.3 3.8 1.7-7-5.4-4.7 7.1-.6z" />
                                </svg>
                                @endfor
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="inline-flex items-center gap-2 rounded-full border border-[#E6E6E6] px-4 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9" />
                    </svg>
                    <span class="text-sm">Google Reviews</span>
                    <span class="text-sm font-semibold text-[#C5A059]">4.9 Stars</span>
                </div>
                <p class="text-sm text-[#6B6B6B]">Based on 12,000+ reviews</p>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- CONTACT --}}
    {{-- ============================================================ --}}
    <section id="contact" class="bg-[#1F1F1F]">
        <div class="container mx-auto px-6 md:px-10 lg:px-[80px] py-16 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20">
                {{-- Contact info --}}
                <div>
                    <h2 class="font-serif text-white text-3xl sm:text-4xl mb-8">Contact</h2>
                    <ul class="space-y-5 text-white/80 text-sm">
                        <li class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#E8883E] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            <span>+91 000 000 0000</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#E8883E] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <path d="m22 7-10 6L2 7" />
                            </svg>
                            <span>hello@laundrix.com</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#E8883E] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                            </svg>
                            <span>WhatsApp: +91 000 000 0000</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#E8883E] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            <span>Kerala • India</span>
                        </li>
                    </ul>
                </div>

                {{-- Contact form --}}
                <form wire:submit="submit" class="bg-[#2A2A2A] rounded-2xl p-8">
                    <h3 class="font-serif text-white text-xl mb-6">
                        Send a message
                    </h3>

                    {{-- Name --}}
                    <label for="name"
                        class="block text-[10px] tracking-[0.1em] uppercase text-white/50 mb-2">
                        Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        wire:model="name"
                        placeholder="Your name"
                        class="w-full rounded-xl bg-[#1F1F1F] border border-white/10 text-white px-4 py-3.5 text-sm placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-[#E8883E]/50">

                    @error('name')
                    <p class="mt-2 mb-5 text-xs text-red-400">{{ $message }}</p>
                    @enderror

                    {{-- Mobile --}}
                    <label for="phone"
                        class="block text-[10px] tracking-[0.1em] uppercase text-white/50 mb-2 mt-5">
                        Mobile
                    </label>

                    <input
                        type="tel"
                        id="phone"
                        wire:model="phone"
                        placeholder="98470 12345"
                        class="w-full rounded-xl bg-[#1F1F1F] border border-white/10 text-white px-4 py-3.5 text-sm placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-[#E8883E]/50">

                    @error('phone')
                    <p class="mt-2 mb-5 text-xs text-red-400">{{ $message }}</p>
                    @enderror

                    {{-- Email --}}
                    <label for="email"
                        class="block text-[10px] tracking-[0.1em] uppercase text-white/50 mb-2 mt-5">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        wire:model="email"
                        placeholder="you@email.com"
                        class="w-full rounded-xl bg-[#1F1F1F] border border-white/10 text-white px-4 py-3.5 text-sm placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-[#E8883E]/50">

                    @error('email')
                    <p class="mt-2 mb-5 text-xs text-red-400">{{ $message }}</p>
                    @enderror

                    {{-- Message --}}
                    <label for="message"
                        class="block text-[10px] tracking-[0.1em] uppercase text-white/50 mb-2 mt-5">
                        Message
                    </label>

                    <textarea
                        id="message"
                        wire:model="message"
                        rows="4"
                        placeholder="How can we help?"
                        class="w-full rounded-xl bg-[#1F1F1F] border border-white/10 text-white px-4 py-3.5 text-sm placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-[#E8883E]/50"></textarea>

                    @error('message')
                    <p class="mt-2 mb-6 text-xs text-red-400">{{ $message }}</p>
                    @enderror

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="w-full rounded-full bg-[#E8883E] text-white text-sm font-semibold py-4 hover:bg-[#d97a30] transition-colors disabled:opacity-60 disabled:cursor-not-allowed">

                        <span wire:loading.remove wire:target="submit">
                            Send Message
                        </span>

                        <span wire:loading wire:target="submit" class="flex items-center justify-center gap-2">
                            <span class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                            Sending...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- FOOTER --}}
    {{-- ============================================================ --}}
    <footer class="bg-[#111111] text-white/70 hidden">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-[120px] py-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
                <div>
                    <p class="font-serif text-white text-2xl mb-4">Laundrix</p>
                    <p class="text-sm max-w-sm leading-relaxed">
                        Kerala's modern laundry &amp; dry-cleaning network-precision, convenience, and a finish that feels premium.
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-8">
                    <div>
                        <p class="text-[#E8883E] text-xs font-semibold tracking-[0.1em] uppercase mb-4">About</p>
                        <ul class="space-y-3 text-sm">
                            <li><a href="#about" class="hover:text-white transition-colors">Our Story</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Careers</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Sustainability</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="text-[#E8883E] text-xs font-semibold tracking-[0.1em] uppercase mb-4">Services</p>
                        <ul class="space-y-3 text-sm">
                            <li><a href="#services" class="hover:text-white transition-colors">Dry Cleaning</a></li>
                            <li><a href="#services" class="hover:text-white transition-colors">Wash &amp; Fold</a></li>
                            <li><a href="#services" class="hover:text-white transition-colors">Premium Laundry</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="text-[#E8883E] text-xs font-semibold tracking-[0.1em] uppercase mb-4">Quick Links</p>
                        <ul class="space-y-3 text-sm">
                            <li><a href="#track-order" class="hover:text-white transition-colors">Track Order</a></li>
                            <li><a href="#reviews" class="hover:text-white transition-colors">Reviews</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="text-[#E8883E] text-xs font-semibold tracking-[0.1em] uppercase mb-4">Connect</p>
                        <ul class="space-y-3 text-sm">
                            <li><a href="#" class="hover:text-white transition-colors">Instagram</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">LinkedIn</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Twitter</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-white/50">
                <p>© {{ date('Y') }} Laundrix. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a href="#" class="hover:text-white transition-colors">Privacy</a>
                    <a href="#" class="hover:text-white transition-colors">Terms</a>
                    <a href="#" class="hover:text-white transition-colors">Cookies</a>
                </div>
            </div>
        </div>
    </footer>

</div>