<!DOCTYPE html>
<html lang="en" x-data>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in · Laundrix</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative grid min-h-screen place-items-center overflow-hidden bg-bg dark:bg-slate-950 px-4 font-body text-text dark:text-slate-100 antialiased">

    <div class="aurora" aria-hidden="true">
        <span></span><span></span><span></span>
    </div>

    <div class="glass relative w-full max-w-md rounded-3xl p-8 sm:p-10" data-hero>
        <div class="flex items-center gap-3">
            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br from-primary to-secondary text-white shadow-lg shadow-primary/30"><x-icon name="sparkles" class="h-6 w-6" /></span>
            <div>
                <p class="font-display text-xl font-bold">Laundrix</p>
                <p class="text-xs text-text-soft">Admin &amp; staff portal</p>
            </div>
        </div>

        <h1 class="mt-8 font-display text-2xl font-bold">Welcome back</h1>
        <p class="mt-1 text-sm text-text-soft">Sign in to manage orders, customers and branches.</p>

        <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-4">
            @csrf
            <label class="block">
                <span class="text-xs font-semibold text-text-soft">Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="mt-1.5 w-full rounded-2xl border border-border bg-white/70 px-4 py-3 text-sm outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/10 dark:border-slate-700 dark:bg-slate-800/70">
            </label>
            <label class="block">
                <span class="text-xs font-semibold text-text-soft">Password</span>
                <input type="password" name="password" required
                       class="mt-1.5 w-full rounded-2xl border border-border bg-white/70 px-4 py-3 text-sm outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/10 dark:border-slate-700 dark:bg-slate-800/70">
            </label>

            @error('email')
                <p class="rounded-xl bg-danger/10 px-3 py-2 text-xs font-medium text-danger">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 text-text-soft">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-border text-primary focus:ring-primary/30">
                    Remember me
                </label>
            </div>

            <button class="btn-primary w-full justify-center">Sign in</button>
        </form>

        <p class="mt-6 text-center text-xs text-text-soft">
            Demo: <span class="font-mono">admin@laundrix.ai</span> / <span class="font-mono">password</span>
        </p>
    </div>
    @livewireScriptConfig
</body>
</html>
