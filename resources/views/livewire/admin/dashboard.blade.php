<div wire:poll.60s class="space-y-6">

    {{-- Period filter --}}
    <div class="flex flex-wrap items-center justify-between gap-3" data-reveal>
        <div>
            <h1 class="font-display text-xl font-bold">Overview</h1>
            <p class="text-sm text-text-soft">Showing data for: <span class="font-semibold text-text">{{ $this->periodLabel() }}</span></p>
        </div>
        <x-admin.date-filter :current="$period" />
    </div>

    {{-- Stat cards --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
        @php
            $cards = [
                ['label' => "Orders", 'value' => $stats['orders_today'], 'icon' => 'shopping-bag', 'tint' => 'from-primary/15 to-primary/5'],
                ['label' => 'Pending', 'value' => $stats['pending'], 'icon' => 'clock', 'tint' => 'from-warning/15 to-warning/5'],
                ['label' => 'Delivered', 'value' => $stats['delivered'], 'icon' => 'check-circle', 'tint' => 'from-success/15 to-success/5'],
                ['label' => "Revenue", 'value' => (int) $stats['revenue_today'], 'icon' => 'banknotes', 'tint' => 'from-secondary/15 to-secondary/5', 'prefix' => '&#8377;'],
                ['label' => 'Customers', 'value' => $stats['customers'], 'icon' => 'users', 'tint' => 'from-primary/15 to-secondary/5'],
                ['label' => 'Active riders', 'value' => $stats['riders_online'], 'icon' => 'truck', 'tint' => 'from-success/15 to-primary/5'],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="card-float glass rounded-3xl p-5" data-reveal>
                <div class="flex items-center justify-between">
                    <span class="grid h-10 w-10 place-items-center rounded-2xl bg-gradient-to-br {{ $card['tint'] }} text-primary"><x-icon :name="$card['icon']" class="h-5 w-5" /></span>
                </div>
                <p class="mt-4 font-display text-3xl font-bold"
                   data-counter="{{ $card['value'] }}"
                   @isset($card['prefix']) data-prefix="&#8377;" @endisset>{!! isset($card['prefix']) ? '&#8377;' : '' !!}{{ number_format($card['value']) }}</p>
                <p class="mt-1 text-sm text-text-soft">{{ $card['label'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        {{-- Revenue chart --}}
        <div class="glass rounded-3xl p-6 xl:col-span-2" data-reveal wire:ignore>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-display text-lg font-semibold">Revenue — last 30 days</h2>
                    <p class="text-sm text-text-soft">Delivered & paid orders</p>
                </div>
                <span class="rounded-full bg-gradient-to-r from-primary/10 to-secondary/10 px-3 py-1 text-xs font-semibold text-primary">Live</span>
            </div>
            <div class="h-72">
                <canvas id="revenueChart"
                        data-labels='@json($revenueSeries['labels'])'
                        data-values='@json($revenueSeries['values'])'></canvas>
            </div>
        </div>

        {{-- AI forecast --}}
        <div class="glass rounded-3xl p-6" data-reveal>
            <div class="flex items-center gap-2 mb-4">
                <span class="grid h-9 w-9 place-items-center rounded-2xl bg-gradient-to-br from-primary to-secondary text-white"><x-icon name="sparkles" class="h-5 w-5" /></span>
                <h2 class="font-display text-lg font-semibold">AI revenue forecast</h2>
            </div>

            @if ($forecast)
                <p class="font-display text-3xl font-bold">&#8377;{{ number_format($forecast['next_month_revenue'] ?? 0) }}</p>
                <p class="mt-1 flex items-center gap-1.5 text-sm font-medium {{ ($forecast['growth_percent'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                    <x-icon :name="($forecast['growth_percent'] ?? 0) >= 0 ? 'arrow-trending-up' : 'arrow-trending-down'" class="h-4 w-4" />
                    {{ abs($forecast['growth_percent'] ?? 0) }}% projected next month
                </p>
                <ul class="mt-5 space-y-3">
                    @foreach (array_slice($forecast['insights'] ?? [], 0, 3) as $insight)
                        <li class="flex gap-2 rounded-2xl bg-white/50 dark:bg-slate-800/50 p-3 text-sm">
                            <span class="text-primary">&#8226;</span>
                            <span>{{ $insight }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="space-y-3">
                    <div class="skeleton h-9 w-2/3 rounded-xl"></div>
                    <div class="skeleton h-4 w-1/2 rounded-lg"></div>
                    <div class="skeleton h-14 rounded-2xl"></div>
                    <div class="skeleton h-14 rounded-2xl"></div>
                </div>
                <p class="mt-4 text-xs text-text-soft">
                    Forecast appears once an <code class="font-mono">OPENAI_API_KEY</code> is configured and enough order history exists.
                </p>
            @endif
        </div>
    </div>
</div>

@script
<script>
    (() => {
        const Chart = window.Chart;
        const el = document.getElementById('revenueChart');
        if (!Chart || !el || el.dataset.rendered) return;
        el.dataset.rendered = '1';

        const labels = JSON.parse(el.dataset.labels);
        const values = JSON.parse(el.dataset.values);
        const ctx = el.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(14,165,233,0.35)');
        gradient.addColorStop(1, 'rgba(37,99,235,0.02)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    data: values,
                    borderColor: '#0EA5E9',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.45,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: '#2563EB',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1200, easing: 'easeOutQuart' },
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 8, color: '#64748B' } },
                    y: { grid: { color: 'rgba(100,116,139,0.12)' }, ticks: { color: '#64748B', callback: v => '\u20B9' + v } },
                },
            },
        });
    })();
</script>
@endscript