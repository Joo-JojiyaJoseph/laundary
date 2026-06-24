<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0EA5E9">
    <title>{{ $title ?? 'Dashboard' }} · Laundrix Admin</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.json">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-bg dark:bg-slate-950 text-text dark:text-slate-100 antialiased font-body"
      x-data="{ sidebar: window.innerWidth >= 1024 }">

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-72 transform transition-transform duration-300 ease-out lg:translate-x-0"
           :class="sidebar ? 'translate-x-0' : '-translate-x-full'">
        <div class="glass m-3 flex h-[calc(100vh-1.5rem)] flex-col rounded-3xl p-5">
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-3 px-2">
                <span class="grid h-10 w-10 place-items-center rounded-2xl bg-gradient-to-br from-primary to-secondary text-white shadow-lg shadow-primary/30"><x-icon name="sparkles" class="h-5 w-5" /></span>
                <span class="font-display text-xl font-bold">Laundrix</span>
            </a>

            <nav class="mt-8 flex-1 space-y-1.5 overflow-y-auto pr-1">
                @php
                    $groups = [
                        'Overview' => [
                            ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'squares-2x2'],
                            ['route' => 'admin.pos', 'match' => 'admin.pos', 'label' => 'POS Terminal', 'icon' => 'calculator'],
                        ],
                        'Operations' => [
                            ['route' => 'admin.orders.index', 'match' => 'admin.orders.*', 'label' => 'Orders', 'icon' => 'shopping-bag'],
                            ['route' => 'admin.payments.index', 'match' => 'admin.payments.*', 'label' => 'Payments', 'icon' => 'banknotes'],
                            ['route' => 'admin.customers.index', 'match' => 'admin.customers.*', 'label' => 'Customers', 'icon' => 'users'],
                            ['route' => 'admin.riders.index', 'match' => 'admin.riders.*', 'label' => 'Riders', 'icon' => 'truck'],
                        ],
                        'Catalogue' => [
                             ['route' => 'admin.categories.index', 'match' => 'admin.categories.*', 'label' => 'Categories', 'icon' => 'tag'],
                            ['route' => 'admin.services.index', 'match' => 'admin.services.*', 'label' => 'Services', 'icon' => 'sparkles'],
                            ['route' => 'admin.products.index', 'match' => 'admin.products.*', 'label' => 'Items & Pricing', 'icon' => 'cube'],
                        ],
                    ];
                @endphp
                @foreach ($groups as $groupLabel => $links)
                    <p class="px-4 pt-3 pb-1 text-[10px] font-bold uppercase tracking-widest text-text-soft/70">{{ $groupLabel }}</p>
                    @foreach ($links as $link)
                        @php $active = request()->routeIs($link['match']); @endphp
                        <a href="{{ route($link['route']) }}" wire:navigate
                           @class([
                               'group flex items-center gap-3 rounded-2xl px-4 py-2.5 text-sm font-medium transition-all duration-200',
                               'bg-gradient-to-r from-primary to-secondary text-white shadow-lg shadow-primary/25' => $active,
                               'text-text-soft hover:bg-white/60 dark:hover:bg-slate-800/60 hover:text-text dark:hover:text-white hover:translate-x-1' => ! $active,
                           ])>
                            <x-icon :name="$link['icon']" class="h-5 w-5 shrink-0" />
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                @endforeach
            </nav>

            <div class="rounded-2xl bg-gradient-to-br from-primary/10 to-secondary/10 p-4">
                <p class="text-sm font-semibold">{{ auth()->user()?->name }}</p>
                <p class="text-xs text-text-soft truncate">{{ auth()->user()?->email }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button class="w-full rounded-xl border border-border dark:border-slate-700 px-3 py-2 text-xs font-semibold text-text-soft transition hover:border-danger hover:text-danger">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Mobile overlay --}}
    <div x-show="sidebar" x-transition.opacity @click="sidebar = false"
         class="fixed inset-0 z-30 bg-slate-900/40 backdrop-blur-sm lg:hidden" x-cloak></div>

    {{-- Main --}}
    <div class="lg:pl-72 min-h-screen flex flex-col">
        <header class="sticky top-0 z-20 m-3 mb-0">
            <div class="glass flex items-center justify-between rounded-2xl px-5 py-3">
                <div class="flex items-center gap-3">
                    <button @click="sidebar = !sidebar"
                            class="grid h-9 w-9 place-items-center rounded-xl border border-border dark:border-slate-700 text-text-soft transition hover:text-primary lg:hidden">
                        <x-icon name="bars-3" class="h-5 w-5" />
                    </button>
                    <h1 class="font-display text-lg font-semibold">{{ $title ?? 'Dashboard' }}</h1>
                </div>
                <div class="flex items-center gap-2">
                </div>
            </div>
        </header>

        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition.opacity.duration.500ms
                 class="mx-3 mt-3 rounded-2xl border border-success/30 bg-success/10 px-5 py-3 text-sm font-medium text-success">
                {{ session('success') }}
            </div>
        @endif

        <main class="flex-1 p-3 sm:p-5">
            {{ $slot }}
        </main>
    </div>

    <x-feedback-modal />
    <x-confirm-modal />

    @livewireScriptConfig
</body>
</html>
