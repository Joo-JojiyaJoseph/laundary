<?php

namespace App\Livewire\Admin\Reports;

use App\Enums\OrderStatus;
use App\Livewire\Concerns\ExportsSpreadsheet;
use App\Livewire\Concerns\WithDateFilter;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Rider;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Orders extends Component
{
    use WithPagination;
    use WithDateFilter;
    use ExportsSpreadsheet;

    public string $customerFilter = '';
    public string $riderFilter = '';
    public string $statusFilter = '';
    public string $paymentFilter = '';

    public function updatedCustomerFilter(): void { $this->resetPage(); }
    public function updatedRiderFilter(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }
    public function updatedPaymentFilter(): void { $this->resetPage(); }

    /** Build the filtered query (shared by the table and the export). */
    protected function baseQuery()
    {
        $user = auth()->user();

        return Order::query()
            ->with(['customer', 'rider.user', 'branch'])
            ->when(! $user->hasRole('super-admin') && $user->branch_id, fn ($q) => $q->where('branch_id', $user->branch_id))
            ->when($this->customerFilter, fn ($q) => $q->where('customer_id', $this->customerFilter))
            ->when($this->riderFilter, fn ($q) => $q->where('rider_id', $this->riderFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->paymentFilter, fn ($q) => $q->where('payment_status', $this->paymentFilter))
            ->tap(fn ($q) => $this->applyDateFilter($q))
            ->latest();
    }

    public function export(): StreamedResponse
    {
        $filename = 'order-report-' . now()->format('Y-m-d_His') . '.xlsx';

        return $this->streamXlsx(
            $filename,
            ['Order No', 'Date', 'Customer', 'Mobile', 'Rider', 'Status', 'Payment status', 'Total', 'Paid', 'Outstanding'],
            function (callable $write) {
                $this->baseQuery()->chunk(500, function ($orders) use ($write) {
                    foreach ($orders as $o) {
                        $write([
                            $o->order_no,
                            $o->created_at,                              // real date -> visible & formatted
                            $o->customer?->name,
                            $o->customer?->mobile,
                            $o->rider?->user?->name ?? '—',
                            $o->status->label(),
                            ucfirst($o->payment_status),
                            (float) $o->total,
                            (float) $o->paid_amount,
                            (float) $o->outstanding,
                        ]);
                    }
                });
            }
        );
    }

    public function render()
    {
        $user = auth()->user();
        $summaryQuery = (clone $this->baseQuery())->reorder();

        $summary = [
            'count' => (clone $summaryQuery)->count(),
            'total' => (float) (clone $summaryQuery)->sum('total'),
            'paid' => (float) (clone $summaryQuery)->sum('paid_amount'),
        ];
        $summary['outstanding'] = max(0, $summary['total'] - $summary['paid']);

        return view('livewire.admin.reports.orders', [
            'orders' => $this->baseQuery()->paginate(20),
            'summary' => $summary,
            'statuses' => OrderStatus::cases(),
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'riders' => Rider::with('user')->get()->sortBy('user.name')->values(),
        ])->layout('layouts.admin', ['title' => 'Order report']);
    }
}
