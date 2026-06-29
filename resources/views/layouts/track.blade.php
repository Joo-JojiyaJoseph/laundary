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

    <main>{{ $slot }}</main>

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
