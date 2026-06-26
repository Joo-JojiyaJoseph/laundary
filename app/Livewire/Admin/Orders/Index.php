<?php

namespace App\Livewire\Admin\Orders;

use App\Enums\OrderStatus;
use App\Livewire\Concerns\WithDateFilter;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use WithDateFilter;

    public string $search = "";
    public string $statusFilter = "";
    public string $paymentFilter = "";

    public function getListeners(): array
    {
        $branchId = auth()->user()->branch_id;

        return $branchId
            ? ["echo-private:branches.{$branchId},.order.status.updated" => '$refresh']
            : [];
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }
    public function updatedPaymentFilter(): void { $this->resetPage(); }

    public function render()
    {
        $user = auth()->user();

        return view("livewire.admin.orders.index", [
            "orders" => Order::with(["customer", "branch"])
                ->when(! $user->hasRole("super-admin") && $user->branch_id, fn ($q) => $q->where("branch_id", $user->branch_id))
                ->when($this->search, fn ($q) => $q->where(fn ($w) => $w
                    ->where("order_no", "like", "%{$this->search}%")
                    ->orWhereHas("customer", fn ($c) => $c->where("name", "like", "%{$this->search}%")
                        ->orWhere("mobile", "like", "%{$this->search}%"))))
                ->when($this->statusFilter, fn ($q) => $q->where("status", $this->statusFilter))
                ->when($this->paymentFilter, fn ($q) => $q->where("payment_status", $this->paymentFilter))
                ->tap(fn ($q) => $this->applyDateFilter($q))
                ->latest()
                ->paginate(15),
            "statuses" => OrderStatus::cases(),
        ])->layout("layouts.admin", ["title" => "Orders"]);
    }
}
