<!DOCTYPE html>
<html lang="en" x-data x-init="$store.theme.init()" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $metaTitle = $title ?? config('app.name');
        $metaDescription =
            $description ??
            "Laundrix is Kerala's modern laundry & dry-cleaning network — doorstep pickup, eco-friendly cleaning, steam ironing and live order tracking from pickup to delivery.";
        $metaKeywords =
            $keywords ??
            'laundry service Kerala, dry cleaning, wash and fold, steam ironing, doorstep laundry pickup, premium laundry, Ernakulam laundry, Muvattupuzha laundry, online laundry tracking, shoe cleaning, stain removal, Laundrix';
        $ogImage = asset('icons/og-image.png');
    @endphp

    {{-- Primary SEO meta --}}
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ $metaKeywords }}">
    <meta name="author" content="Laundrix">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Favicons & app icons --}}
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
    <meta name="theme-color" content="#0EA5E9">
    <link rel="manifest" href="/manifest.json">

    {{-- Open Graph (Facebook, WhatsApp, LinkedIn) --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Laundrix">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="en_IN">

    {{-- Twitter card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Instrument+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-white font-body text-text antialiased">

    {{-- ── Nav ──────────────────────────────────────────────── --}}
    <header class="hidden fixed inset-x-0 top-0 z-50" x-data="{ open: false, scrolled: false }" x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 40)">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 transition-all duration-300"
            :class="scrolled ? 'mt-0 bg-white/90 shadow-soft backdrop-blur-xl' : 'mt-2 bg-transparent'">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <span
                    class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-primary to-secondary shadow-lg shadow-primary/30 overflow-hidden">

                    <svg class="h-9 w-9" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">

                        <defs>
                            <linearGradient id="laundryBlue" x1="0" y1="0" x2="1"
                                y2="1">
                                <stop offset="0%" stop-color="#20C8FF" />
                                <stop offset="100%" stop-color="#0878F9" />
                            </linearGradient>
                        </defs>

                        <!-- background -->
                        <rect width="512" height="512" rx="120" fill="url(#laundryBlue)" />

                        <!-- bubbles -->
                        <g fill="white" opacity=".85">
                            <circle cx="110" cy="130" r="20" />
                            <circle cx="400" cy="150" r="15" />
                            <circle cx="420" cy="320" r="18" />
                            <circle cx="90" cy="330" r="12" />
                        </g>

                        <!-- sparkle -->
                        <path d="M380 90 L395 130 L435 145 L395 160 L380 200 L365 160 L325 145 L365 130Z"
                            fill="white" />

                        <!-- washing machine -->
                        <rect x="140" y="140" width="230" height="250" rx="35" fill="white"
                            stroke="#063B8F" stroke-width="14" />

                        <!-- cloth -->
                        <path d="M150 180
                     C180 130 230 140 255 175
                     L210 220 L150 230Z" fill="#7DE3FF" stroke="#063B8F" stroke-width="10" />

                        <!-- door -->
                        <circle cx="255" cy="285" r="80" fill="#38BDF8" stroke="#063B8F"
                            stroke-width="14" />

                        <!-- water -->
                        <path d="M190 300
                     C220 260 250 325 285 285
                     C310 255 340 290 340 320
                     C300 350 220 355 175 320Z" fill="#E0F7FF" />

                        <!-- controls -->
                        <circle cx="300" cy="180" r="10" fill="#0878F9" />
                        <circle cx="335" cy="180" r="10" fill="#0878F9" />

                    </svg>

                </span>

                <span class="font-display text-2xl font-extrabold" :class="scrolled ? 'text-text' : 'text-white'">
                    Laundrix
                </span>
            </a>

            <div class="hidden items-center gap-8 text-sm font-semibold md:flex"
                 :class="scrolled ? 'text-slate-600' : 'text-white/90'">
                @foreach (["#home" => "Home", "#about" => "About", "#services" => "Services", "#track" => "Track order", "#feedback" => "Reviews", "#contact" => "Contact"] as $r => $label)
                    <a href="{{ url('/') . $r }}" class="transition hover:text-primary">{{ $label }}</a>
                @endforeach
            </div>

            <div class="flex items-center gap-3">
                <a href="/admin/dashboard" class="btn-primary !rounded-full !px-6 !py-2.5 hidden sm:inline-flex">Sign
                    in</a>
                <button class="grid h-10 w-10 place-items-center rounded-full border md:hidden"
                    :class="scrolled ? 'border-slate-200 text-slate-700' : 'border-white/40 text-white'"
                    @click="open = !open" aria-label="Menu">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round" />
                    </svg>
                </button>
            </div>

            {{-- mobile sheet --}}
            <div x-show="open" x-cloak x-transition.opacity
                class="absolute inset-x-3 top-16 rounded-3xl glass p-5 md:hidden" @click.outside="open = false">
                <div class="grid gap-3 text-sm font-medium">
                    @foreach (["#home" => "Home", "#services" => "Services", "#track" => "Track order", "#about" => "About", "#feedback" => "Reviews", "#contact" => "Contact"] as $r => $label)
                        <a href="{{ url('/') . $r }}" @click="open = false" class="rounded-xl px-3 py-2 hover:bg-slate-100 dark:hover:bg-white/5">{{ $label }}</a>
                    @endforeach
                    <a href="/admin/dashboard" class="btn-primary mt-2">Sign in</a>
                </div>
            </div>
        </nav>
    </header>

    <header class="absolute top-0 left-0 right-0 z-30">
        <nav class="container mx-auto px-6 md:px-10 lg:px-[80px] flex items-center justify-between py-6" aria-label="Primary navigation">
            <a href="{{ url('/') }}" class="font-serif text-2xl tracking-tight text-white">Laundrix</a>

            <ul class="hidden lg:flex items-center gap-10 text-sm font-medium text-white/90">
                <li><a href="#home" class="hover:text-white transition-colors">Home</a></li>
                <li><a href="#about" class="hover:text-white transition-colors">About</a></li>
                <li><a href="#services" class="hover:text-white transition-colors">Services</a></li>
                <li><a href="#track-order" class="hover:text-white transition-colors">Track Order</a></li>
                <li><a href="#reviews" class="hover:text-white transition-colors">Reviews</a></li>
                <li><a href="#contact" class="hover:text-white transition-colors">Contact</a></li>
            </ul>

            <a href="\login"
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
    </header>
    

    <main>{{ $slot }}</main>

    {{-- ── WhatsApp float ───────────────────────────────────── --}}
    <a href="https://wa.me/919000000000" target="_blank" rel="noopener"
        class="hidden fixed bottom-24 right-6 z-40 grid h-13 w-13 place-items-center rounded-full bg-[#25D366] p-3.5 text-white shadow-float transition hover:scale-110"
        aria-label="Chat on WhatsApp">
        <svg class="size-6" viewBox="0 0 24 24" fill="currentColor">
            <path
                d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.1 14.2c-.2.6-1.2 1.2-1.7 1.2-.4.1-1 .1-1.6-.1-3-1-5-3.5-5.2-3.7-.1-.2-1.2-1.6-1.2-3.1s.8-2.2 1-2.5c.3-.3.6-.3.8-.3h.6c.2 0 .4 0 .6.5l.9 2.1c.1.2.1.4 0 .6l-.4.6c-.1.2-.3.4-.1.7.1.3.7 1.2 1.6 1.9 1.1.9 1.9 1.2 2.2 1.3.3.1.5.1.6-.1l.7-.9c.2-.3.4-.2.7-.1l1.9.9c.3.1.5.2.5.4 0 0 .1.4-.1.6z" />
        </svg>
    </a>

    {{-- ── Footer ───────────────────────────────────────────── --}}
    {{-- ── Footer ─────────────────────────────────────────────
    <footer class="relative overflow-hidden bg-[#0b1220] text-slate-300">
        <div class="bubbles opacity-40" aria-hidden="true">
            @for ($i = 0; $i < 7; $i++)
                <i
                    style="left: {{ 8 + $i * 13 }}%; width: {{ 12 + ($i % 3) * 8 }}px; height: {{ 12 + ($i % 3) * 8 }}px; animation-duration: {{ 13 + $i * 2 }}s; animation-delay: {{ $i }}s;"></i>
            @endfor
        </div>
        <div class="relative mx-auto grid max-w-7xl gap-12 px-6 py-16 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <span
                        class="grid h-10 w-10 place-items-center rounded-2xl bg-gradient-to-br from-primary to-secondary text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8">
                            <path d="M3 13c2-3 5-3 7 0s5 3 7 0 4-2 4-2" stroke-linecap="round" />
                            <circle cx="17" cy="7" r="2.5" />
                        </svg>
                    </span>
                    <span class="font-display text-xl font-extrabold text-white">Laundrix</span>
                </div>
                <p class="mt-4 max-w-xs text-sm text-slate-400">Kerala's modern laundry &amp; dry-cleaning network.
                    Pickup to delivery, tracked live — every thread cared for.</p>
                <div class="mt-5 space-y-2.5 text-sm">
                    <p class="flex items-center gap-3"><span
                            class="grid h-8 w-8 place-items-center rounded-full bg-primary/15 text-primary"><x-icon
                                name="phone" class="h-4 w-4" /></span> +91 90000 00000</p>
                    <p class="flex items-center gap-3"><span
                            class="grid h-8 w-8 place-items-center rounded-full bg-primary/15 text-primary"><x-icon
                                name="envelope" class="h-4 w-4" /></span> hello@laundrix.ai</p>
                    <p class="flex items-center gap-3"><span
                            class="grid h-8 w-8 place-items-center rounded-full bg-primary/15 text-primary"><x-icon
                                name="clock" class="h-4 w-4" /></span> Mon to Sat · 9 AM – 8 PM</p>
                </div>
            </div>

            <div>
                <p class="font-display text-lg font-bold text-white">Our Services</p>
                <span class="mt-1 block h-1 w-10 rounded-full bg-gradient-to-r from-primary to-secondary"></span>
                <ul class="mt-5 grid grid-cols-1 gap-2.5 text-sm text-slate-400">
                    @foreach (['Dry Cleaning', 'Wash & Fold', 'Premium Laundry', 'Steam Ironing', 'Shoe Cleaning', 'Stain Removal'] as $s)
                        <li><a href="{{ url('/') }}#services" class="transition hover:text-primary">→
                                {{ $s }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <p class="font-display text-lg font-bold text-white">Quick Links</p>
                <span class="mt-1 block h-1 w-10 rounded-full bg-gradient-to-r from-primary to-secondary"></span>
                <ul class="mt-5 space-y-2.5 text-sm text-slate-400">
                    <li><a href="{{ url('/') }}#about" class="transition hover:text-primary">→ About us</a></li>
                    <li><a href="{{ url('/') }}#track" class="transition hover:text-primary">→ Track order</a>
                    </li>
                    <li><a href="{{ url('/') }}#contact" class="transition hover:text-primary">→ Contact</a>
                    </li>
                    <li><a href="/admin/dashboard" class="transition hover:text-primary">→ Staff sign in</a></li>
                </ul>
            </div>
            <div>
                <p class="font-display text-lg font-bold text-white">Follow us</p>
                <span class="mt-1 block h-1 w-10 rounded-full bg-gradient-to-r from-primary to-secondary"></span>
                <ul class="mt-5 flex items-center gap-3">
                    @foreach (['facebook', 'twitter', 'linkedin', 'whatsapp'] as $n)
                        <li><a href="#"
                                class="grid h-9 w-9 place-items-center rounded-full bg-white/10 transition hover:bg-white hover:text-primary">
                                <span class="text-xs font-bold uppercase">{{ substr($n, 0, 1) }}</span>
                            </a></li>
                    @endforeach
                </ul>
        </div>
        </div>

        <div class="relative bg-gradient-to-r from-primary to-secondary">
            <div
                class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-6 py-4 text-sm text-white">
                <p>© {{ date('Y') }} Laundrix. All rights reserved.</p>
                <div class="flex items-center gap-2">
                    @foreach (['facebook', 'twitter', 'linkedin', 'whatsapp'] as $n)
                        <a href="#"
                            class="grid h-9 w-9 place-items-center rounded-full bg-white/20 transition hover:bg-white hover:text-primary">
                            <span class="text-xs font-bold uppercase">{{ substr($n, 0, 1) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer> --}}

    <footer class="bg-[#111111] text-white/70">
        <div class="container mx-auto px-6 md:px-10 lg:px-[80px] py-16">
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

    {{-- Cookie consent --}}
    <div x-data="{ ok: localStorage.cookieOk }" x-show="!ok" x-cloak
        class="fixed bottom-6 left-6 z-40 max-w-sm rounded-2xl glass p-4 text-sm text-slate-600 dark:text-slate-300">
        We use cookies for sign-in and analytics.
        <button class="ml-2 font-semibold text-primary-600" @click="localStorage.cookieOk = 1; ok = 1">Okay</button>
    </div>
    <x-feedback-modal />
    @livewireScriptConfig
</body>

</html>
