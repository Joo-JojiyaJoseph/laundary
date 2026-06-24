<div>
    @php
        $heroImg     = 'https://images.unsplash.com/photo-1545173168-9f1947eebb7f?auto=format&fit=crop&w=1200&q=80';
        $aboutImg    = 'https://images.unsplash.com/photo-1582735689369-4fe89db7114c?auto=format&fit=crop&w=900&q=80';
        $aboutImg2   = 'https://images.unsplash.com/photo-1604176354204-9268737828e4?auto=format&fit=crop&w=600&q=80';
        $svcImgs = [
            'https://images.unsplash.com/photo-1610557892470-55d9e80c0bce?auto=format&fit=crop&w=700&q=80',
            'https://images.unsplash.com/photo-1626806787461-102c1bfaaea1?auto=format&fit=crop&w=700&q=80',
            'https://images.unsplash.com/photo-1489274495757-95c7c837b101?auto=format&fit=crop&w=700&q=80',
            'https://images.unsplash.com/photo-1567113463300-102a7eb3cb26?auto=format&fit=crop&w=700&q=80',
            'https://images.unsplash.com/photo-1545173168-9f1947eebb7f?auto=format&fit=crop&w=700&q=80',
            'https://images.unsplash.com/photo-1521656693074-0ef32e80a5d5?auto=format&fit=crop&w=700&q=80',
        ];
        $ctaImg = 'https://images.unsplash.com/photo-1604335398980-eded8e1b5d11?auto=format&fit=crop&w=1400&q=80';
    @endphp

    {{-- HERO --}}
    <section id="home" class="relative min-h-[92vh] overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ $heroImg }}" alt="" class="h-full w-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-[#0b2a52]/95 via-[#0b2a52]/80 to-transparent"></div>
        </div>
        <div class="bubbles" aria-hidden="true">
            @for ($i = 0; $i < 9; $i++)
                <i style="left: {{ 4 + $i * 11 }}%; width: {{ 12 + ($i % 4) * 8 }}px; height: {{ 12 + ($i % 4) * 8 }}px; animation-duration: {{ 10 + $i * 2 }}s; animation-delay: {{ $i }}s;"></i>
            @endfor
        </div>
        <div class="relative mx-auto flex min-h-[92vh] max-w-7xl items-center px-6">
            <div class="max-w-2xl py-24 text-white">
                <p data-hero class="section-eyebrow !text-primary-200">We clean, you shine</p>
                <h1 data-hero class="mt-5 font-display text-5xl font-extrabold leading-[1.05] sm:text-7xl">
                    Quality Laundry<br><span class="bg-gradient-to-r from-sky-300 to-primary-200 bg-clip-text text-transparent">Every Thread</span>
                </h1>
                <p data-hero class="mt-6 max-w-lg text-lg text-sky-100/85">
                    Kerala's modern laundry &amp; dry-cleaning network. We pick up, wash, steam-iron and deliver
                    every garment back to your door — and you can follow it live, every step of the way.
                </p>
                <div data-hero class="mt-9 flex flex-wrap gap-4">
                    <a href="#services" data-magnetic class="btn-primary !rounded-full !px-8 !py-3.5">Discover more</a>
                    <a href="#contact" class="inline-flex items-center rounded-full border-2 border-white/60 px-8 py-3.5 text-sm font-semibold text-white transition hover:border-white hover:bg-white/10">Contact us</a>
                </div>
                <div data-hero class="mt-12 flex flex-wrap gap-10">
                    <div><p class="font-display text-4xl font-bold" data-counter="48000" data-suffix="+">0</p><p class="text-sm text-sky-100/70">garments / month</p></div>
                    <div><p class="font-display text-4xl font-bold" data-counter="120" data-suffix="+">0</p><p class="text-sm text-sky-100/70">partner stores</p></div>
                    <div><p class="font-display text-4xl font-bold" data-counter="98" data-suffix="%">0</p><p class="text-sm text-sky-100/70">on-time delivery</p></div>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURE STRIP --}}
    <section class="relative z-10 mx-auto -mt-16 max-w-7xl px-6">
        <div class="grid gap-6 md:grid-cols-3">
            @foreach ([
                ['icon' => 'face-smile', 'title' => '100% Happiness Guarantee', 'text' => 'High-quality detergents and fabric-safe care on every single order.'],
                ['icon' => 'truck', 'title' => 'Free Collection & Delivery', 'text' => 'Doorstep pickup and delivery across the Ernakulam district at no cost.'],
                ['icon' => 'chat-bubble-left-right', 'title' => '24/7 Dedicated Support', 'text' => 'Talk to a real human on call or WhatsApp, any time of day.'],
            ] as $f)
                <div class="feature-card text-center" data-reveal data-tilt="6">
                    <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-gradient-to-br from-primary-50 to-primary-100 text-primary-600">
                        <x-icon :name="$f['icon']" class="h-8 w-8" />
                    </span>
                    <h3 class="mt-5 font-display text-lg font-bold">{{ $f['title'] }}</h3>
                    <p class="mt-2 text-sm text-slate-500">{{ $f['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ABOUT --}}
    <section id="about" class="mx-auto max-w-7xl px-6 py-28">
        <div class="grid items-center gap-14 lg:grid-cols-2">
            <div class="relative" data-reveal data-tilt="5">
                <span class="tilt-glare"></span>
                <div class="tilt-photo aspect-[4/3]"><img src="{{ $aboutImg }}" alt="Steam ironing in a Kerala laundry"></div>
                <div class="absolute -right-4 -bottom-8 h-44 w-44 overflow-hidden rounded-full border-8 border-white shadow-float">
                    <img src="{{ $aboutImg2 }}" alt="Fresh pressed shirts" class="h-full w-full object-cover">
                </div>
                <div class="absolute -left-5 top-8 rounded-3xl bg-gradient-to-br from-primary to-secondary px-7 py-5 text-white shadow-float">
                    <p class="font-display text-4xl font-extrabold">24<span class="text-2xl">+</span></p>
                    <p class="text-sm font-semibold">Years</p>
                </div>
            </div>
            <div data-reveal>
                <p class="section-eyebrow">About us · Born in Kerala 🌴</p>
                <h2 class="mt-4 font-display text-4xl font-extrabold leading-tight sm:text-5xl">Experience The Pinnacle<br>Of Laundry Excellence</h2>
                <p class="mt-5 max-w-md text-slate-500">
                    From everyday wash &amp; fold to delicate kasavu sarees, we handle every fabric with
                    eco-friendly chemistry, garment-level tagging and live tracking — built around the
                    rhythm of Kerala's monsoons and festivals.
                </p>
                <div class="mt-7 grid max-w-md grid-cols-2 gap-x-8 gap-y-3 text-sm">
                    @foreach (['Pickup & Delivery Service', 'Energy-Efficient Machines', 'Same-Day / Express Service', 'Folding Preferences', 'Kasavu & Handloom Care', 'Satisfaction Guarantee'] as $point)
                        <p class="flex items-center gap-2.5"><x-icon name="check-circle" class="h-5 w-5 shrink-0 text-primary-600" /> {{ $point }}</p>
                    @endforeach
                </div>
                <a href="#contact" data-magnetic class="btn-primary mt-9 !rounded-full !px-8">More about us</a>
            </div>
        </div>
    </section>

    {{-- SERVICES --}}
    <section id="services" class="bg-gradient-to-b from-primary-50/40 to-white py-28">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center" data-reveal>
                <p class="section-eyebrow center justify-center">Our best services</p>
                <h2 class="mt-3 font-display text-4xl font-extrabold sm:text-5xl">Our Best Laundry Services For You!</h2>
            </div>
            <div class="mt-14 grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($services as $i => $service)
                    <div class="group overflow-hidden rounded-3xl bg-white shadow-soft transition hover:shadow-float" data-reveal>
                        <div class="relative h-52 overflow-hidden">
                            <img src="{{ $svcImgs[$i % count($svcImgs)] }}" alt="{{ $service->name }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-110">
                            <span class="absolute -bottom-7 left-1/2 grid h-16 w-16 -translate-x-1/2 place-items-center rounded-full bg-gradient-to-br from-primary to-secondary text-white shadow-float">
                                <x-icon :name="$service->icon ?: 'sparkles'" class="h-7 w-7" />
                            </span>
                        </div>
                        <div class="px-6 pb-7 pt-12 text-center">
                            <h3 class="font-display text-lg font-bold">{{ $service->name }}</h3>
                            <p class="mt-2 line-clamp-3 text-sm text-slate-500">{{ $service->description ?: 'Professional fabric care with eco-friendly chemistry and live order tracking.' }}</p>
                            <a href="#track" class="mt-5 inline-flex rounded-full border border-border px-6 py-2 text-xs font-bold uppercase tracking-wider text-slate-600 transition hover:border-primary hover:text-primary">Track order</a>
                        </div>
                    </div>
                @empty
                    @for ($i = 0; $i < 6; $i++)<div class="skeleton h-80 rounded-3xl"></div>@endfor
                @endforelse
            </div>
        </div>
    </section>

    {{-- WORK PROCESS — modern connected cards --}}
    <section class="relative overflow-hidden bg-gradient-to-b from-white to-primary-50/40 py-28">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center" data-reveal>
                <p class="section-eyebrow center justify-center">Work process</p>
                <h2 class="mt-3 font-display text-4xl font-extrabold sm:text-5xl">How We Work It!</h2>
                <p class="mx-auto mt-4 max-w-lg text-slate-500">Three simple steps from your doorstep, back to your wardrobe — tracked live the whole way.</p>
            </div>

            <div class="relative mt-16 grid gap-8 md:grid-cols-3">
                {{-- connecting dashed line --}}
                <div class="absolute inset-x-[18%] top-16 hidden border-t-2 border-dashed border-primary-200 md:block"></div>

                @foreach ([
                    ['icon' => 'sparkles', 'step' => '01', 'title' => 'Schedule Your Service', 'text' => 'Request a pickup by phone or WhatsApp — choose a slot that suits you.'],
                    ['icon' => 'beaker', 'step' => '02', 'title' => 'Expert Cleaning Process', 'text' => 'Garments are tagged, sorted by fabric and cleaned by trained specialists.'],
                    ['icon' => 'truck', 'step' => '03', 'title' => 'Packaging & Delivery', 'text' => 'Neatly packed in eco-friendly wrap and delivered back to your door.'],
                ] as $p)
                    <div class="group relative" data-reveal data-tilt="6">
                        <span class="tilt-glare"></span>
                        <div class="relative h-full rounded-3xl border border-border bg-white p-8 text-center shadow-soft transition duration-300 group-hover:-translate-y-2 group-hover:shadow-float">
                            {{-- floating number badge --}}
                            <span class="absolute -top-5 left-1/2 grid h-10 w-16 -translate-x-1/2 place-items-center rounded-full bg-gradient-to-r from-primary to-secondary font-display text-sm font-bold text-white shadow-lg shadow-primary/30">
                                {{ $p['step'] }}
                            </span>
                            {{-- animated icon ring --}}
                            <span class="relative mx-auto mt-4 grid h-20 w-20 place-items-center rounded-2xl bg-gradient-to-br from-primary-50 to-primary-100 text-primary-600 transition group-hover:scale-110">
                                <span class="absolute inset-0 rounded-2xl ring-2 ring-primary/0 transition group-hover:ring-primary/40 group-hover:ring-offset-2"></span>
                                <x-icon :name="$p['icon']" class="h-9 w-9" />
                            </span>
                            <h3 class="mt-6 font-display text-xl font-bold">{{ $p['title'] }}</h3>
                            <p class="mt-3 text-sm leading-relaxed text-slate-500">{{ $p['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CUSTOMER FEEDBACK (phone-verified) --}}
    <section id="feedback" class="bg-gradient-to-b from-white to-primary-50/40 py-28">
        <div class="mx-auto max-w-7xl px-6">
            <livewire:public.feedback-section />
        </div>
    </section>

    {{-- TRACK ORDER --}}
    <section id="track" class="relative overflow-hidden bg-gradient-to-br from-[#0b2a52] to-[#0f1e33] py-28 text-white">
        <div class="bubbles" aria-hidden="true">
            @for ($i = 0; $i < 6; $i++)<i style="left: {{ 10 + $i * 15 }}%; width: {{ 14 + ($i%3)*10 }}px; height: {{ 14 + ($i%3)*10 }}px; animation-duration: {{ 12 + $i*2 }}s; animation-delay: {{ $i }}s;"></i>@endfor
        </div>
        <div class="relative mx-auto max-w-3xl px-6 text-center">
            <p class="section-eyebrow center justify-center !text-primary-200">Track order</p>
            <h2 class="mt-3 font-display text-4xl font-extrabold sm:text-5xl">Where's my laundry?</h2>
            <p class="mx-auto mt-3 max-w-lg text-sky-100/80">Enter the invoice number from your bill and your registered mobile — or scan the QR on the invoice for instant tracking.</p>
            <div class="mx-auto mt-10 max-w-lg text-left" data-reveal>
                <livewire:track.track-lookup-form />
            </div>
        </div>
    </section>

    {{-- CTA BAND --}}
    <section class="mx-auto max-w-7xl px-6 py-24">
        <div class="contact-band px-8 py-12 sm:px-14">
            <div class="absolute inset-0 opacity-25"><img src="{{ $ctaImg }}" alt="" class="h-full w-full object-cover"></div>
            <div class="relative flex flex-wrap items-center justify-between gap-8 text-white">
                <div data-reveal>
                    <p class="section-eyebrow !text-primary-300">Get free contact for services</p>
                    <h2 class="mt-3 max-w-md font-display text-3xl font-extrabold sm:text-4xl">You Get Premium Laundry Service From Us!</h2>
                </div>
                <div class="flex flex-wrap gap-4" data-reveal>
                    <a href="#services" data-magnetic class="btn-primary !rounded-full !px-8 !py-3.5">Get our services</a>
                    <a href="#contact" class="inline-flex items-center rounded-full bg-white px-8 py-3.5 text-sm font-semibold text-[#0b2a52] transition hover:scale-105">Contact us now</a>
                </div>
            </div>
        </div>
    </section>

    {{-- CONTACT --}}
    <section id="contact" class="mx-auto max-w-7xl px-6 pb-28">
        <div class="grid gap-12 lg:grid-cols-2">
            <div data-reveal>
                <p class="section-eyebrow">Contact</p>
                <h2 class="mt-3 font-display text-4xl font-extrabold sm:text-5xl">Talk to a human</h2>
                <p class="mt-4 max-w-md text-slate-500">Questions about a stain, bulk pricing for your hotel, or opening a franchise? We reply fast.</p>
                <div class="mt-8 space-y-4">
                    <a href="tel:+919000000000" class="flex items-center gap-4 rounded-2xl border border-border bg-white p-4 transition hover:border-primary/50 hover:shadow-soft">
                        <span class="grid h-12 w-12 place-items-center rounded-xl bg-gradient-to-br from-primary to-secondary text-white"><x-icon name="phone" class="h-5 w-5" /></span>
                        <span><span class="block text-xs text-slate-500">Call 24/7 for service</span><span class="font-display text-lg font-bold">+91 90000 00000</span></span>
                    </a>
                    <a href="mailto:hello@laundrix.ai" class="flex items-center gap-4 rounded-2xl border border-border bg-white p-4 transition hover:border-primary/50 hover:shadow-soft">
                        <span class="grid h-12 w-12 place-items-center rounded-xl bg-gradient-to-br from-primary to-secondary text-white"><x-icon name="envelope" class="h-5 w-5" /></span>
                        <span><span class="block text-xs text-slate-500">Email us anytime</span><span class="font-display text-lg font-bold">hello@laundrix.ai</span></span>
                    </a>
                    <a href="https://wa.me/919000000000" target="_blank" class="flex items-center gap-4 rounded-2xl border border-border bg-white p-4 transition hover:border-success/50 hover:shadow-soft">
                        <span class="grid h-12 w-12 place-items-center rounded-xl bg-success/10 text-success"><x-icon name="chat-bubble-left-right" class="h-5 w-5" /></span>
                        <span><span class="block text-xs text-slate-500">WhatsApp</span><span class="font-display text-lg font-bold">Chat with support</span></span>
                    </a>
                    <div class="flex items-center gap-4 rounded-2xl border border-border bg-white p-4">
                        <span class="grid h-12 w-12 place-items-center rounded-xl bg-gradient-to-br from-primary to-secondary text-white"><x-icon name="map-pin" class="h-5 w-5" /></span>
                        <span><span class="block text-xs text-slate-500">Head office</span><span class="font-semibold">MC Road, Muvattupuzha, Kerala 686661</span></span>
                    </div>
                </div>
            </div>
            <div data-reveal>
                <livewire:public.contact-section />
            </div>
        </div>
    </section>

    <button x-data="{ show: false }"
            x-init="window.addEventListener('scroll', () => show = window.scrollY > 600)"
            x-show="show" x-transition.opacity
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="to-top fixed bottom-6 right-6 z-40 grid h-12 w-12 place-items-center rounded-full border-2 border-primary-200 bg-white text-primary shadow-float hover:scale-110" x-cloak>
        <x-icon name="arrow-up" class="h-5 w-5" />
    </button>
</div>
