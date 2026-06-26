@props([
    'periods' => ['all' => 'All', 'today' => 'Today', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year'],
    'custom' => true,
])
{{-- Pairs with App\Livewire\Concerns\WithDateFilter (period / dateFrom / dateTo). --}}
<div class="flex flex-wrap items-center gap-2" x-data="{ showCustom: @entangle('period').live === 'custom' }"
     x-init="$watch('$wire.period', v => showCustom = v === 'custom')">
    <div class="flex flex-wrap rounded-2xl border border-border p-1 dark:border-slate-700">
        @foreach ($periods as $value => $label)
            <button type="button" wire:click="$set('period', '{{ $value }}')"
                    @class([
                        'rounded-xl px-3 py-1.5 text-xs font-semibold transition',
                        'bg-gradient-to-r from-primary to-secondary text-white shadow' => false,
                    ])
                    :class="$wire.period === '{{ $value }}'
                        ? 'bg-gradient-to-r from-primary to-secondary text-white shadow'
                        : 'text-text-soft hover:text-primary'">
                {{ $label }}
            </button>
        @endforeach
        @if ($custom)
            <button type="button" wire:click="$set('period', 'custom')"
                    class="rounded-xl px-3 py-1.5 text-xs font-semibold transition"
                    :class="$wire.period === 'custom'
                        ? 'bg-gradient-to-r from-primary to-secondary text-white shadow'
                        : 'text-text-soft hover:text-primary'">
                Custom
            </button>
        @endif
    </div>

    @if ($custom)
        <div x-show="showCustom" x-cloak class="flex flex-wrap items-center gap-2">
            <input type="date" wire:model.live="dateFrom" class="input !w-auto !py-1.5 text-xs" aria-label="From date">
            <span class="text-xs text-text-soft">to</span>
            <input type="date" wire:model.live="dateTo" class="input !w-auto !py-1.5 text-xs" aria-label="To date">
        </div>
    @endif
</div>
