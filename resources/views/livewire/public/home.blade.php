<div class="font-sans bg-[#FAFAF8] text-[#1F1F1F] antialiased">

    {{-- ============================================================ --}}
    {{-- NAVIGATION --}}
    {{-- ============================================================ --}}
    <!-- <header class="absolute top-0 left-0 right-0 z-30">
        <nav class="max-w-[1920px] mx-auto flex items-center justify-between px-6 sm:px-10 lg:px-20 py-6" aria-label="Primary navigation">
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
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
        </nav>
    </header> -->

    {{-- ============================================================ --}}
    {{-- HERO --}}
    {{-- ============================================================ --}}
    <section id="home" class="relative">
        <div class="relative h-screen overflow-hidden">
            <img src="/images/home/ded64d6282a3c67edffa1fcd2a1e90f03dc47e4a.jpg"
                alt="Freshly folded towels in warm afternoon light"
                class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>

            <div class="relative h-full w-screen mx-auto px-6 sm:px-10 lg:px-25 flex flex-col justify-center pt-20">
                <div class="max-w-xl">
                    <p class="text-[#E8883E] text-xs sm:text-sm font-semibold tracking-[0.15em] uppercase mb-4">
                        Kerala's Modern Laundry Network
                    </p>
                    <h1 class="font-serif text-white text-4xl sm:text-5xl lg:text-6xl leading-[1.1] mb-6">
                        Quality Laundry<br>Every Thread
                    </h1>
                    <p class="text-white/80 text-sm sm:text-base leading-relaxed mb-8 max-w-md">
                        Kerala's modern laundry &amp; dry-cleaning network. We pick up, wash, steam-iron and deliver every garment back to your door — and you can follow it live, every step of the way.
                    </p>
                    <div class="flex flex-wrap items-center gap-4">
                        <a href="#about"
                            class="inline-flex items-center justify-center rounded-full bg-[#E8883E] text-white text-sm font-semibold px-7 py-3.5 hover:bg-[#d97a30] transition-colors">
                            Discover More
                        </a>
                        <a href="#contact"
                            class="inline-flex items-center justify-center rounded-full border border-white/70 text-white text-sm font-semibold px-7 py-3.5 hover:bg-white hover:text-[#1F1F1F] transition-colors">
                            Contact Us
                        </a>
                    </div>
                </div>

                <div class="pt-15">
                    <div class="grid grid-cols-3 gap-x-4 gap-y-2 text-white w-full">
                        <div class="w-full border p-4 rounded-2xl backdrop-blur-2xl border-white">
                            <p class="text-[10px] tracking-[0.15em] uppercase text-white/60 mb-1">Garments / Month</p>
                            <p class="font-serif text-xl sm:text-2xl">48,000+</p>
                        </div>
                        <div class="w-full border p-4 rounded-2xl backdrop-blur-2xl border-white">
                            <p class="text-[10px] tracking-[0.15em] uppercase text-white/60 mb-1">Partner Stores</p>
                            <p class="font-serif text-xl sm:text-2xl">120+</p>
                        </div>
                        <div class="w-full border p-4 rounded-2xl backdrop-blur-2xl border-white">
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
    <section class="bg-[#FAFAF8] mt-20">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-20 mt-8 sm:-mt-10 relative z-10 pb-16 lg:pb-24">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6">
                <div class="bg-white rounded-2xl border border-[#E6E6E6] shadow-sm px-7 py-6">
                    <p class="font-serif text-3xl mb-2">48,000+</p>
                    <p class="text-sm text-[#6B6B6B]">Garments processed with precision every month.</p>
                </div>
                <div class="bg-white rounded-2xl border border-[#E6E6E6] shadow-sm px-7 py-6">
                    <p class="font-serif text-3xl mb-2">120+</p>
                    <p class="text-sm text-[#6B6B6B]">Partner stores across Kerala, unified by one standard.</p>
                </div>
                <div class="bg-white rounded-2xl border border-[#E6E6E6] shadow-sm px-7 py-6">
                    <p class="font-serif text-3xl mb-2">98%</p>
                    <p class="text-sm text-[#6B6B6B]">On-time delivery rate-built for your schedule.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- WHY LAUNDRIX --}}
    {{-- ============================================================ --}}
    <section class="bg-[#F3F2EF]">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-20 py-16 lg:py-24">
            <p class="text-[#E8883E] text-xs font-semibold tracking-[0.15em] uppercase mb-3">Why Laundrix</p>
            <h2 class="font-serif text-3xl sm:text-4xl mb-12">Designed for the way you live.</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6">
                {{-- Card 1 --}}
                <div class="bg-white rounded-2xl border border-[#E6E6E6] p-7">
                    <div class="w-11 h-11 rounded-full bg-[#FBEAE0] flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#E8883E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
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
                <div class="bg-white rounded-2xl border border-[#E6E6E6] p-7">
                    <div class="w-11 h-11 rounded-full bg-[#FBEAE0] flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#E8883E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
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
                <div class="bg-white rounded-2xl border border-[#E6E6E6] p-7">
                    <div class="w-11 h-11 rounded-full bg-[#FBEAE0] flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#E8883E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
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
    <section id="about" class="bg-[#FAFAF8]">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-20 py-16 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                {{-- Text --}}
                <div>
                    <p class="text-[#E8883E] text-xs font-semibold tracking-[0.15em] uppercase mb-3">Our Story</p>
                    <h2 class="font-serif text-3xl sm:text-4xl leading-tight mb-6">Experience The Pinnacle Of Laundry Excellence</h2>
                    <p class="text-sm sm:text-base text-[#6B6B6B] leading-relaxed mb-8 max-w-md">
                        From everyday wash &amp; fold to delicate kasavu sarees, we handle every fabric with eco-friendly chemistry, garment-level tagging and live tracking — built around the rhythm of Kerala's monsoons and festivals.
                    </p>
                    <a href="#services"
                        class="inline-flex items-center justify-center rounded-full bg-[#E8883E] text-white text-sm font-semibold px-7 py-3.5 hover:bg-[#d97a30] transition-colors">
                        Learn more
                    </a>
                </div>

                {{-- Image collage --}}
                <div class="grid grid-cols-3 grid-rows-2 gap-3 h-[280px] sm:h-[340px] lg:h-[380px]">
                    <img src="https://images.unsplash.com/photo-1517677208171-0bc6725a3e60?q=80&w=800&auto=format&fit=crop"
                        alt="Folded laundry being carried" class="col-span-1 row-span-2 w-full h-full object-cover rounded-xl">
                    <img src="https://images.unsplash.com/photo-1545173168-9f1947eebb7f?q=80&w=800&auto=format&fit=crop"
                        alt="Washing machine in a bright laundromat" class="col-span-2 row-span-1 w-full h-full object-cover rounded-xl">
                    <img src="https://images.unsplash.com/photo-1582735689369-4fe89db7114c?q=80&w=800&auto=format&fit=crop"
                        alt="Laundry attendant smiling while sorting clothes" class="col-span-1 row-span-1 w-full h-full object-cover rounded-xl">
                    <img src="https://images.unsplash.com/photo-1604335399105-a0c585fd81a1?q=80&w=800&auto=format&fit=crop"
                        alt="Neatly hung shirts" class="col-span-1 row-span-1 w-full h-full object-cover rounded-xl">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- OUR SERVICES --}}
    {{-- ============================================================ --}}
    <section id="services" class="bg-[#F3F2EF]">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-20 py-16 lg:py-24">
            <p class="text-[#E8883E] text-xs font-semibold tracking-[0.15em] uppercase mb-3">Our Services</p>
            <h2 class="font-serif text-3xl sm:text-4xl mb-12">Care tailored to every fabric.</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                @php
                $services = [
                ['title' => 'Dry Cleaning', 'img' => 'https://images.unsplash.com/photo-1521656693074-0ef32e80a5d5?q=80&w=900&auto=format&fit=crop'],
                ['title' => 'Wash & Fold', 'img' => 'https://images.unsplash.com/photo-1620626011761-996317b8d101?q=80&w=900&auto=format&fit=crop'],
                ['title' => 'Premium Laundry', 'img' => 'https://images.unsplash.com/photo-1604335399105-a0c585fd81a1?q=80&w=900&auto=format&fit=crop'],
                ['title' => 'Steam Ironing', 'img' => 'https://images.unsplash.com/photo-1632933296211-a30f0813cd72?q=80&w=900&auto=format&fit=crop'],
                ['title' => 'Shoe Cleaning', 'img' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?q=80&w=900&auto=format&fit=crop'],
                ['title' => 'Curtain Cleaning', 'img' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=900&auto=format&fit=crop'],
                ];
                @endphp

                @foreach ($services as $service)
                <article class="bg-white rounded-2xl border border-[#E6E6E6] overflow-hidden">
                    <img src="{{ $service['img'] }}" alt="{{ $service['title'] }} service" class="w-full h-44 object-cover">
                    <div class="p-6">
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
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- HOW IT WORKS --}}
    {{-- ============================================================ --}}
    <section class="bg-[#FAFAF8]">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-20 py-16 lg:py-24">
            <p class="text-[#E8883E] text-xs font-semibold tracking-[0.15em] uppercase mb-3">How It Works</p>
            <h2 class="font-serif text-3xl sm:text-4xl mb-14">Simple, seamless, and completely transparent.</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-10 lg:gap-6">
                {{-- Step 1 --}}
                <div class="relative">
                    <div class="hidden sm:block absolute top-4 left-[calc(100%-1rem)] w-[calc(100%-2rem)] border-t border-[#E6E6E6]"></div>
                    <div class="w-9 h-9 rounded-full bg-[#E8883E] text-white text-sm font-semibold flex items-center justify-center mb-5 relative z-10">01</div>
                    <h3 class="font-serif text-lg mb-2">Schedule Pickup</h3>
                    <p class="text-sm text-[#6B6B6B] leading-relaxed max-w-xs">Book online or in-app. We'll collect your garments at a time that works for you.</p>
                </div>
                {{-- Step 2 --}}
                <div class="relative">
                    <div class="hidden sm:block absolute top-4 left-[calc(100%-1rem)] w-[calc(100%-2rem)] border-t border-[#E6E6E6]"></div>
                    <div class="w-9 h-9 rounded-full bg-[#E8883E] text-white text-sm font-semibold flex items-center justify-center mb-5 relative z-10">02</div>
                    <h3 class="font-serif text-lg mb-2">Professional Cleaning</h3>
                    <p class="text-sm text-[#6B6B6B] leading-relaxed max-w-xs">Our specialists assess, clean, and finish every item with precision and care.</p>
                </div>
                {{-- Step 3 --}}
                <div class="relative">
                    <div class="w-9 h-9 rounded-full bg-[#E8883E] text-white text-sm font-semibold flex items-center justify-center mb-5 relative z-10">03</div>
                    <h3 class="font-serif text-lg mb-2">Delivered To You</h3>
                    <p class="text-sm text-[#6B6B6B] leading-relaxed max-w-xs">Your order is hand-delivered, folded, and ready to wear.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- TRACK ORDER --}}
    {{-- ============================================================ --}}
    <section id="track-order" class="bg-[#F3F2EF]">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-20 py-16 lg:py-24 flex flex-col items-center text-center">
            <p class="text-[#E8883E] text-xs font-semibold tracking-[0.15em] uppercase mb-3">Track Your Order</p>
            <h2 class="font-serif text-3xl sm:text-4xl mb-10">Where is your order?</h2>

            <form class="w-full max-w-md bg-white rounded-2xl border border-[#E6E6E6] shadow-sm p-8 text-left">
                <h3 class="font-serif text-lg mb-6">Track your order</h3>

                <label for="invoice_number" class="block text-[10px] tracking-[0.1em] uppercase text-[#6B6B6B] mb-2">Invoice Number</label>
                <input type="text" id="invoice_number" name="invoice_number" placeholder="Enter invoice number"
                    class="w-full rounded-xl border border-[#E6E6E6] px-4 py-3 text-sm mb-5 placeholder:text-[#A8A8A8] focus:outline-none focus:ring-2 focus:ring-[#E8883E]/40">

                <label for="mobile_number" class="block text-[10px] tracking-[0.1em] uppercase text-[#6B6B6B] mb-2">Mobile Number</label>
                <input type="tel" id="mobile_number" name="mobile_number" placeholder="+91 000 000 0000"
                    class="w-full rounded-xl border border-[#E6E6E6] px-4 py-3 text-sm mb-6 placeholder:text-[#A8A8A8] focus:outline-none focus:ring-2 focus:ring-[#E8883E]/40">

                <button type="submit"
                    class="w-full rounded-full bg-[#E8883E] text-white text-sm font-semibold py-3.5 hover:bg-[#d97a30] transition-colors mb-4">
                    Track Order
                </button>

                <p class="text-xs text-[#6B6B6B] text-center">
                    Need help? <a href="#contact" class="text-[#1F1F1F] font-medium underline-offset-2 hover:underline">Contact support →</a>
                </p>
            </form>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- CTA BANNER --}}
    {{-- ============================================================ --}}
    <section class="relative">
        <div class="relative h-[360px] sm:h-[420px] overflow-hidden">
            <img src="https://images.unsplash.com/photo-1556910103-1c02745aae4d?q=80&w=2400&auto=format&fit=crop"
                alt="Stack of folded towels" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/65"></div>

            <div class="relative h-full max-w-2xl mx-auto px-6 flex flex-col items-center justify-center text-center">
                <h2 class="font-serif text-white text-3xl sm:text-4xl leading-tight mb-4">Your Clothes Deserve Better Care.</h2>
                <p class="text-white/80 text-sm sm:text-base mb-8 max-w-md">
                    Experience Kerala's modern laundry network-precision, convenience, and a finish that feels premium.
                </p>
                <a href="#track-order"
                    class="inline-flex items-center justify-center rounded-full bg-[#E8883E] text-white text-sm font-semibold px-7 py-3.5 hover:bg-[#d97a30] transition-colors">
                    Book Free Pickup
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- TESTIMONIALS --}}
    {{-- ============================================================ --}}
    <section id="reviews" class="bg-[#FAFAF8]">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-20 py-16 lg:py-24">
            <p class="text-[#E8883E] text-xs font-semibold tracking-[0.15em] uppercase mb-3">What Our Customers Say</p>
            <h2 class="font-serif text-3xl sm:text-4xl mb-12">Consistently exceptional.</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6 mb-10">
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
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-20 py-16 lg:py-24">
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
                <form class="bg-[#2A2A2A] rounded-2xl p-8">
                    <h3 class="font-serif text-white text-xl mb-6">Send a message</h3>

                    <label for="name" class="block text-[10px] tracking-[0.1em] uppercase text-white/50 mb-2">Name</label>
                    <input type="text" id="name" name="name" placeholder="Your name"
                        class="w-full rounded-xl bg-[#1F1F1F] border border-white/10 text-white px-4 py-3 text-sm mb-5 placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-[#E8883E]/50">

                    <label for="email" class="block text-[10px] tracking-[0.1em] uppercase text-white/50 mb-2">Email</label>
                    <input type="email" id="email" name="email" placeholder="you@email.com"
                        class="w-full rounded-xl bg-[#1F1F1F] border border-white/10 text-white px-4 py-3 text-sm mb-5 placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-[#E8883E]/50">

                    <label for="message" class="block text-[10px] tracking-[0.1em] uppercase text-white/50 mb-2">Message</label>
                    <textarea id="message" name="message" rows="4" placeholder="How can we help?"
                        class="w-full rounded-xl bg-[#1F1F1F] border border-white/10 text-white px-4 py-3 text-sm mb-6 placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-[#E8883E]/50"></textarea>

                    <button type="submit"
                        class="w-full rounded-full bg-[#E8883E] text-white text-sm font-semibold py-3.5 hover:bg-[#d97a30] transition-colors">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- FOOTER --}}
    {{-- ============================================================ --}}
    <!-- <footer class="bg-[#111111] text-white/70">
        <div class="max-w-[1920px] mx-auto px-6 sm:px-10 lg:px-20 py-16">
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
    </footer> -->

</div>