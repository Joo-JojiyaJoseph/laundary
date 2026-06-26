<?php

namespace App\Livewire\Admin\Reports;

use App\Livewire\Concerns\ExportsSpreadsheet;
use App\Livewire\Concerns\WithDateFilter;
use App\Models\Customer;
use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Payments extends Component
{
    use WithPagination;
    use WithDateFilter;
    use ExportsSpreadsheet;

    public string $customerFilter = '';
    public string $methodFilter = '';
    public string $typeFilter = '';

    public function updatedCustomerFilter(): void { $this->resetPage(); }
    public function updatedMethodFilter(): void { $this->resetPage(); }
    public function updatedTypeFilter(): void { $this->resetPage(); }

    protected function baseQuery()
    {
        $user = auth()->user();
        $branchScope = fn ($q) => $q->when(! $user->hasRole('super-admin') && $user->branch_id,
            fn ($w) => $w->where('branch_id', $user->branch_id));

        return Payment::query()
            ->with(['order', 'customer', 'receivedBy'])
            ->whereHas('order', $branchScope)
            ->when($this->customerFilter, fn ($q) => $q->where('customer_id', $this->customerFilter))
            ->when($this->methodFilter, fn ($q) => $q->where('method', $this->methodFilter))
            ->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter))
            ->tap(fn ($q) => $this->applyDateFilter($q))
            ->latest();
    }

    public function export(): StreamedResponse
    {
        $filename = 'payment-report-' . now()->format('Y-m-d_His') . '.xlsx';

        return $this->streamXlsx(
            $filename,
            ['Date', 'Order No', 'Customer', 'Method', 'Type', 'Reference', 'Received by', 'Amount'],
            function (callable $write) {
                $this->baseQuery()->chunk(500, function ($payments) use ($write) {
                    foreach ($payments as $p) {
                        $write([
                            $p->created_at,                              // real date -> visible & formatted
                            $p->order?->order_no,
                            $p->customer?->name,
                            ucfirst(str_replace('_', ' ', $p->method)),
                            ucfirst($p->type),
                            $p->reference ?? '',
                            $p->receivedBy?->name ?? 'System',
                            (float) $p->amount,
                        ]);
                    }
                });
            }
        );
    }

    public function render()
    {
        $summaryQuery = (clone $this->baseQuery())->reorder();

        $summary = [
            'count' => (clone $summaryQuery)->count(),
            'collected' => (float) (clone $summaryQuery)->where('type', '!=', 'refund')->sum('amount'),
            'refunded' => (float) (clone $summaryQuery)->where('type', 'refund')->sum('amount'),
        ];
        $summary['net'] = $summary['collected'] - $summary['refunded'];

        return view('livewire.admin.reports.payments', [
            'payments' => $this->baseQuery()->paginate(20),
            'summary' => $summary,
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
        ])->layout('layouts.admin', ['title' => 'Payment report']);
    }
}
