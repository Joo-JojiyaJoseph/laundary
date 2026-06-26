<div>
    <x-admin.page-header title="Customer feedback" subtitle="Approve reviews before they appear on the public website.">
        <span class="badge badge-warning">{{ $pendingCount }} pending</span>
        <span class="badge badge-success">{{ $approvedCount }} live</span>
    </x-admin.page-header>

    <div class="glass rounded-3xl p-4 sm:p-5" data-reveal>
        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
            <div class="flex rounded-2xl border border-border p-1 dark:border-slate-700">
                @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'all' => 'All'] as $value => $label)
                    <button wire:click="$set('statusFilter', '{{ $value }}')"
                            class="rounded-xl px-3 py-1.5 text-sm font-semibold transition {{ $statusFilter === $value ? 'bg-gradient-to-r from-primary to-secondary text-white shadow' : 'text-text-soft hover:text-primary' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search name or text…" class="input sm:max-w-xs">
            <div class="sm:ml-auto">
                <x-admin.date-filter :current="$period" />
            </div>
        </div>

        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($items as $item)
                <div wire:key="fb-{{ $item->id }}" class="flex h-full flex-col rounded-2xl border border-border/70 p-5 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <x-icon name="solid-star" class="h-4 w-4 {{ $i <= $item->rating ? 'text-amber-400' : 'text-slate-300' }}" />
                            @endfor
                        </div>
                        @if ($item->is_approved)
                            <span class="badge badge-success">Live</span>
                        @else
                            <span class="badge badge-warning">Pending</span>
                        @endif
                    </div>

                    <p class="mt-3 flex-1 text-sm leading-relaxed text-text-soft">“{{ $item->message }}”</p>

                    <div class="mt-4 border-t border-border/60 pt-3">
                        <p class="text-sm font-semibold">{{ $item->name }}</p>
                        <p class="text-xs text-text-soft">{{ $item->created_at->format('d M Y, h:i A') }}</p>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @if ($item->is_approved)
                            <button wire:click="unapprove({{ $item->id }})" class="btn-soft">Hide</button>
                        @else
                            <button wire:click="approve({{ $item->id }})" class="btn-primary !px-4 !py-1.5 !text-xs">Approve</button>
                        @endif
                        <button @click="$dispatch('confirm', { title: 'Delete feedback?', message: 'This permanently removes the review.', confirmText: 'Yes, delete', method: 'delete', params: [{{ $item->id }}] })" class="btn-danger-soft">Delete</button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-text-soft">
                    <x-icon name="chat-bubble-left-right" class="mx-auto mb-2 h-8 w-8 text-primary/50" />
                    No feedback {{ $statusFilter === 'pending' ? 'awaiting approval' : 'found' }}.
                </div>
            @endforelse
        </div>

        <div class="mt-5">{{ $items->links() }}</div>
    </div>
</div>
