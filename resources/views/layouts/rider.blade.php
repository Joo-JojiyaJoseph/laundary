<!DOCTYPE html>
<html lang="en" x-data class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0EA5E9">
    <title>{{ $title ?? 'Rider' }} · Laundrix</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-bg font-body text-text antialiased">
    <header class="sticky top-0 z-20 border-b border-border/70 bg-white/80 backdrop-blur-xl">
        <div class="mx-auto flex max-w-3xl items-center justify-between px-4 py-3">
            <div class="flex items-center gap-2.5">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-primary to-secondary text-white"><x-icon name="truck" class="h-5 w-5" /></span>
                <div>
                    <p class="font-display text-sm font-bold leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-text-soft">{{ auth()->user()->rider?->vehicle_number ?: 'Laundrix rider' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn-soft">Sign out</button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-4 py-5">
        {{ $slot }}
    </main>

    <x-feedback-modal />
    @livewireScriptConfig
</body>
</html>
