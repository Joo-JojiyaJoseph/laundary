<?php

namespace App\Livewire\Admin\Feedback;

use App\Livewire\Concerns\WithDateFilter;
use App\Models\Feedback;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use WithDateFilter;

    public string $search = '';

    /** pending|approved|all */
    public string $statusFilter = 'pending';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function approve(int $id): void
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);
        $this->dispatch('notify', type: 'success', message: 'Feedback approved — now visible on the website.');
    }

    public function unapprove(int $id): void
    {
        Feedback::whereKey($id)->update([
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);
        $this->dispatch('notify', type: 'success', message: 'Feedback hidden from the website.');
    }

    public function delete(int $id): void
    {
        Feedback::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Feedback deleted.');
    }

    public function render()
    {
        $base = fn () => Feedback::query();

        return view('livewire.admin.feedback.index', [
            'items' => Feedback::with('approvedBy')
                ->when($this->statusFilter === 'pending', fn ($q) => $q->where('is_approved', false))
                ->when($this->statusFilter === 'approved', fn ($q) => $q->where('is_approved', true))
                ->when($this->search, fn ($q) => $q->where(fn ($w) => $w
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('message', 'like', "%{$this->search}%")))
                ->tap(fn ($q) => $this->applyDateFilter($q))
                ->latest()
                ->paginate(12),
            'pendingCount' => Feedback::where('is_approved', false)->count(),
            'approvedCount' => Feedback::where('is_approved', true)->count(),
        ])->layout('layouts.admin', ['title' => 'Feedback']);
    }
}
