<?php

namespace App\Livewire\Admin\Payments;

use App\Livewire\Concerns\WithDateFilter;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use WithDateFilter;

    public string $tab = "outstanding"; // outstanding|payments — outstanding shown first
    public string $search = "";
    public string $methodFilter = "";

    // Record-payment modal
    public bool $showModal = false;
    public string $orderSearch = "";
    public $selectedOrderId = null;
    public $amount = "";
    public string $method = "cash";
    public string $reference = "";

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedMethodFilter(): void { $this->resetPage(); }
    public function updatedTab(): void { $this->resetPage(); }

    public function openModal(?int $orderId = null): void
    {
        $this->reset(["orderSearch", "selectedOrderId", "amount", "reference"]);
        $this->method = "cash";

        if ($orderId) {
            $this->selectOrder($orderId);
        }

        $this->resetValidation();
        $this->showModal = true;
    }

    public function selectOrder(int $orderId): void
    {
        $order = Order::findOrFail($orderId);
        $this->selectedOrderId = $order->id;
        $this->amount = number_format($order->outstanding, 2, ".", "");
        $this->orderSearch = "";
    }

    public function recordPayment(): void
    {
        $this->selectedOrderId = $this->selectedOrderId ?: null;

        $order = $this->selectedOrderId ? Order::find($this->selectedOrderId) : null;

        $this->validate([
            "selectedOrderId" => "required|exists:orders,id",
            "amount" => "required|numeric|min:0.01" . ($order ? "|max:" . max(0.01, $order->outstanding) : ""),
            "method" => "required|in:cash,upi,card,bank_transfer",
            "reference" => "nullable|string|max:100",
        ], ["selectedOrderId.required" => "Pick an order first."], ["selectedOrderId" => "order", "amount" => "amount"]);

        try {
            DB::transaction(function () use ($order) {
                Payment::create([
                    "order_id" => $order->id,
                    "customer_id" => $order->customer_id,
                    "received_by" => auth()->id(),
                    "method" => $this->method,
                    "type" => "payment",
                    "amount" => (float) $this->amount,
                    "reference" => $this->reference ?: null,
                ]);

                $paid = (float) $order->paid_amount + (float) $this->amount;
                $order->update([
                    "paid_amount" => $paid,
                    "payment_status" => $paid >= (float) $order->total ? "paid" : "partial",
                ]);
            });

            $this->showModal = false;
            $this->dispatch("notify", type: "success", title: "Payment recorded", message: "₹" . number_format((float) $this->amount, 2) . " received for {$order->order_no}.");
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch("notify", type: "error", title: "Could not record payment", message: $e->getMessage());
        }
    }

    public function render()
    {
        $user = auth()->user();
        $branchScope = fn ($q) => $q->when(! $user->hasRole("super-admin") && $user->branch_id,
            fn ($w) => $w->where("branch_id", $user->branch_id));

        return view("livewire.admin.payments.index", [
            "payments" => Payment::with(["order", "customer", "receivedBy"])
                ->whereHas("order", $branchScope)
                ->when($this->search, fn ($q) => $q->where(fn ($w) => $w
                    ->whereHas("order", fn ($o) => $o->where("order_no", "like", "%{$this->search}%"))
                    ->orWhereHas("customer", fn ($c) => $c->where("name", "like", "%{$this->search}%"))))
                ->when($this->methodFilter, fn ($q) => $q->where("method", $this->methodFilter))
                ->tap(fn ($q) => $this->applyDateFilter($q))
                ->latest()
                ->paginate(15),
            "outstanding" => Order::with("customer")
                ->tap($branchScope)
                ->whereIn("payment_status", ["unpaid", "partial"])
                ->whereColumn("paid_amount", "<", "total")
                ->latest()
                ->paginate(15, ["*"], "duePage"),
            "dueOrders" => strlen($this->orderSearch) >= 2
                ? Order::with("customer")->tap($branchScope)
                    ->whereColumn("paid_amount", "<", "total")
                    ->where(fn ($w) => $w->where("order_no", "like", "%{$this->orderSearch}%")
                        ->orWhereHas("customer", fn ($c) => $c->where("name", "like", "%{$this->orderSearch}%")
                            ->orWhere("mobile", "like", "%{$this->orderSearch}%")))
                    ->take(6)->get()
                : collect(),
            "selectedOrder" => $this->selectedOrderId ? Order::with("customer")->find($this->selectedOrderId) : null,
            "todayTotal" => Payment::whereHas("order", $branchScope)->whereDate("created_at", today())->sum("amount"),
            "dueTotal" => Order::tap($branchScope)->whereIn("payment_status", ["unpaid", "partial"])
                ->selectRaw("COALESCE(SUM(total - paid_amount), 0) as due")->value("due"),
        ])->layout("layouts.admin", ["title" => "Payments"]);
    }
}
