<div class="min-h-screen bg-bg dark:bg-slate-950 py-10 px-4" wire:key="track-{{ $order->id }}">
    <div class="mx-auto max-w-2xl">

        {{-- Header card --}}
        <div class="glass rounded-3xl p-6 sm:p-8 mb-6" data-reveal>
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-text-soft">Live tracking</p>
                    <h1 class="mt-1 font-display text-2xl sm:text-3xl font-bold text-text dark:text-white">
                        {{ $order->order_no }}
                    </h1>
                    <p class="mt-1 text-sm text-text-soft">{{ $order->invoice?->invoice_no ? "Invoice " . $order->invoice->invoice_no . " · " : "" }}{{ $order->branch?->name }}</p>
                </div>
                <div class="flex items-center gap-2 rounded-full bg-gradient-to-r from-primary to-secondary px-4 py-2 text-white shadow-lg shadow-primary/25">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white/70 opacity-75"></span>
                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-white"></span>
                    </span>
                    <span class="text-sm font-semibold">{{ $order->status->label() }}</span>
                </div>
            </div>

            {{-- Progress bar --}}
            @php
                $pipeline = \App\Enums\OrderStatus::pipeline();
                $idx = array_search($order->status, $pipeline);
                $percent = $idx === false ? 0 : (int) round(($idx) / (count($pipeline) - 1) * 100);
            @endphp
            <div class="mt-6">
                <div class="flex justify-between text-xs font-medium text-text-soft mb-2">
                    <span>Pickup</span>
                    <span>{{ $percent }}%</span>
                    <span>Delivered</span>
                </div>
                <div class="h-2.5 rounded-full bg-border/60 dark:bg-slate-800 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-primary to-secondary transition-all duration-700 ease-out"
                         style="width: {{ max($percent, 4) }}%"></div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-5">
            {{-- Timeline --}}
            <div class="glass rounded-3xl p-6 sm:p-8 sm:col-span-3" data-reveal>
                <h2 class="font-display text-lg font-semibold text-text dark:text-white mb-6">Order journey</h2>
                <ol class="relative space-y-6">
                    @foreach ($pipeline as $i => $status)
                        @php
                            $reached = $idx !== false && $i <= $idx;
                            $active = $idx !== false && $i === $idx;
                            $log = $order->statusLogs->firstWhere('status', $status->value);
                        @endphp
                        <li class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <span @class([
                                    'timeline-dot grid h-9 w-9 place-items-center rounded-full text-base transition',
                                    'done bg-gradient-to-br from-primary to-secondary text-white shadow-md shadow-primary/30' => $reached && ! $active,
                                    'active bg-gradient-to-br from-primary to-secondary text-white shadow-lg shadow-primary/40' => $active,
                                    'bg-border/50 dark:bg-slate-800 text-text-soft' => ! $reached,
                                ])><x-icon :name="$status->icon()" class="h-4 w-4" /></span>
                                @unless ($loop->last)
                                    <span @class([
                                        'mt-1 w-px flex-1 min-h-6',
                                        'bg-gradient-to-b from-primary to-secondary' => $reached && ! $active,
                                        'bg-border dark:bg-slate-800' => ! ($reached && ! $active),
                                    ])></span>
                                @endunless
                            </div>
                            <div class="pb-1">
                                <p @class([
                                    'font-semibold',
                                    'text-text dark:text-white' => $reached,
                                    'text-text-soft' => ! $reached,
                                ])>{{ $status->label() }}</p>
                                @if ($log)
                                    <p class="text-xs text-text-soft mt-0.5">{{ $log->created_at->format('d M, h:i A') }}</p>
                                @elseif ($active)
                                    <p class="text-xs text-primary font-medium mt-0.5">In progress…</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- Summary --}}
            <div class="sm:col-span-2 space-y-6">
                <div class="glass rounded-3xl p-6" data-reveal>
                    <h3 class="font-display text-sm font-semibold uppercase tracking-wider text-text-soft mb-3">Expected delivery</h3>
                    <p class="text-2xl font-bold font-display text-text dark:text-white">
                        {{ $order->delivery_expected_at?->format('d M Y') ?? 'To be scheduled' }}
                    </p>
                    @if ($order->delivery_expected_at)
                        <p class="text-sm text-text-soft mt-1">{{ $order->delivery_expected_at->format('h:i A') }}</p>
                    @endif
                </div>

                <div class="glass rounded-3xl p-6" data-reveal>
                    <h3 class="font-display text-sm font-semibold uppercase tracking-wider text-text-soft mb-4">Items ({{ $order->items->count() }})</h3>
                    <ul class="divide-y divide-border/60 dark:divide-slate-800">
                        @foreach ($order->items as $item)
                            <li class="flex items-center justify-between py-2.5 text-sm">
                                <div>
                                    <p class="font-medium text-text dark:text-white">{{ $item->product?->name ?? $item->name }}</p>
                                    <p class="text-xs text-text-soft">x{{ $item->qty }} &middot; {{ $item->tag_code }}</p>
                                </div>
                                <span class="font-semibold text-text dark:text-white">&#8377;{{ number_format($item->line_total, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 flex items-center justify-between rounded-2xl bg-gradient-to-r from-primary/10 to-secondary/10 px-4 py-3">
                        <span class="text-sm font-semibold text-text dark:text-white">Total</span>
                        <span class="font-display text-lg font-bold text-text dark:text-white">&#8377;{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <p class="mt-8 text-center text-xs text-text-soft">
            This page updates live as your order moves through our facility. Powered by Laundrix.
        </p>
    </div>
</div>