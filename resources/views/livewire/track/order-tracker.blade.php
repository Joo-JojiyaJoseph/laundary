<div class="min-h-screen bg-[#FAFAF8] py-10 sm:py-16 px-6 sm:px-10" wire:key="track-{{ $order->id }}">
    <div class="mx-auto max-w-4xl mt-10">

        {{-- Header card --}}
        <div class="bg-white rounded-3xl border border-[#E6E6E6] shadow-sm p-6 sm:p-8 mb-6" data-reveal>
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#E8883E]">Live tracking</p>
                    <h1 class="mt-1 font-serif text-2xl sm:text-3xl text-[#1F1F1F]">
                        {{ $order->order_no }}
                    </h1>
                    <p class="mt-1 text-sm text-[#6B6B6B]">{{ $order->invoice?->invoice_no ? "Invoice " . $order->invoice->invoice_no . " · " : "" }}{{ $order->branch?->name }}</p>
                </div>
                <div class="flex items-center gap-2 rounded-full bg-[#E8883E] px-4 py-2 text-white shadow-sm">
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
                <div class="flex justify-between text-xs font-medium text-[#6B6B6B] mb-2">
                    <span>Pickup</span>
                    <span>{{ $percent }}%</span>
                    <span>Delivered</span>
                </div>
                <div class="h-2.5 rounded-full bg-[#E6E6E6] overflow-hidden">
                    <div class="h-full rounded-full bg-[#E8883E] transition-all duration-700 ease-out"
                         style="width: {{ max($percent, 4) }}%"></div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-5">
            {{-- Timeline --}}
            <div class="bg-white rounded-3xl border border-[#E6E6E6] shadow-sm p-6 sm:p-8 lg:col-span-3" data-reveal>
                <h2 class="font-serif text-lg text-[#1F1F1F] mb-6">Order journey</h2>
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
                                    'timeline-dot grid h-9 w-9 shrink-0 place-items-center rounded-full text-base transition',
                                    'done bg-[#E8883E] text-white shadow-sm' => $reached && ! $active,
                                    'active bg-[#E8883E] text-white shadow-md ring-4 ring-[#E8883E]/20 animate-pulse' => $active,
                                    'bg-[#F3F2EF] text-[#A8A8A8]' => ! $reached,
                                ])><x-icon :name="$status->icon()" class="h-4 w-4" /></span>
                                @unless ($loop->last)
                                    <span @class([
                                        'mt-1 w-px flex-1 min-h-6',
                                        'bg-[#E8883E]' => $reached && ! $active,
                                        'bg-[#E6E6E6]' => ! ($reached && ! $active),
                                    ])></span>
                                @endunless
                            </div>
                            <div class="pb-1">
                                <p @class([
                                    'font-medium',
                                    'text-[#1F1F1F]' => $reached,
                                    'text-[#A8A8A8]' => ! $reached,
                                ])>{{ $status->label() }}</p>
                                @if ($log)
                                    <p class="text-xs text-[#6B6B6B] mt-0.5">{{ $log->created_at->format('d M, h:i A') }}</p>
                                @elseif ($active)
                                    <p class="text-xs text-[#E8883E] font-medium mt-0.5">In progress…</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- Summary --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl border border-[#E6E6E6] shadow-sm p-6" data-reveal>
                    <h3 class="text-[10px] font-semibold uppercase tracking-[0.1em] text-[#6B6B6B] mb-3">Expected delivery</h3>
                    <p class="font-serif text-2xl text-[#1F1F1F]">
                        {{ $order->delivery_expected_at?->format('d M Y') ?? 'To be scheduled' }}
                    </p>
                    @if ($order->delivery_expected_at)
                        <p class="text-sm text-[#6B6B6B] mt-1">{{ $order->delivery_expected_at->format('h:i A') }}</p>
                    @endif
                </div>

                <div class="bg-white rounded-3xl border border-[#E6E6E6] shadow-sm p-6" data-reveal>
                    <h3 class="text-[10px] font-semibold uppercase tracking-[0.1em] text-[#6B6B6B] mb-4">Items ({{ $order->items->count() }})</h3>
                    <ul class="divide-y divide-[#E6E6E6]">
                        @foreach ($order->items as $item)
                            <li class="flex items-center justify-between py-2.5 text-sm gap-3">
                                <div class="min-w-0">
                                    <p class="font-medium text-[#1F1F1F] truncate">{{ $item->product?->name ?? $item->name }}</p>
                                    <p class="text-xs text-[#6B6B6B]">x{{ $item->qty }} &middot; {{ $item->tag_code }}</p>
                                </div>
                                <span class="font-semibold text-[#1F1F1F] shrink-0">&#8377;{{ number_format($item->line_total, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 flex items-center justify-between rounded-2xl bg-[#FBEAE0] px-4 py-3">
                        <span class="text-sm font-semibold text-[#1F1F1F]">Total</span>
                        <span class="font-serif text-lg text-[#1F1F1F]">&#8377;{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <p class="mt-8 text-center text-xs text-[#6B6B6B]">
            This page updates live as your order moves through our facility. Powered by Laundrix.
        </p>
    </div>
</div>