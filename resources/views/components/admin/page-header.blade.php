@props(['title', 'subtitle' => null])
<div class="mb-5 flex flex-wrap items-center justify-between gap-3" data-reveal>
    <div>
        <h1 class="font-display text-xl font-bold">{{ $title }}</h1>
        @if ($subtitle) <p class="text-sm text-text-soft">{{ $subtitle }}</p> @endif
    </div>
    <div class="flex items-center gap-2">{{ $slot }}</div>
</div>
