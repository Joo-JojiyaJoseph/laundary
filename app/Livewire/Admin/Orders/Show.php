<?php

namespace App\Livewire\Admin\Orders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Rider;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Show extends Component
{
    public Order $order;

    public string $paymentAmount = "";
    public string $paymentMethod = "cash";
    public string $paymentReference = "";

    public $rider_id = null;

    public function mount(Order $order): void
    {
        $this->order = $order->load(["customer", "branch", "rider.user", "items.product", "statusLogs", "payments.receivedBy", "invoice"]);
        $this->rider_id = $order->rider_id;
    }

    public function getListeners(): array
    {
        return ["echo:orders.{$this->order->id},.order.status.updated" => "refreshOrder"];
    }

    public function refreshOrder(): void
    {
        $this->order = $this->order->fresh(["customer", "branch", "rider.user", "items.product", "statusLogs", "payments.receivedBy", "invoice"]);
    }

    public function advanceStatus(): void
    {
        $pipeline = OrderStatus::pipeline();
        $index = array_search($this->order->status, $pipeline);

        if ($index === false || ! isset($pipeline[$index + 1])) return;

        $this->order->transitionTo($pipeline[$index + 1], auth()->user());
        $this->refreshOrder();
        $this->dispatch("notify", type: "success", message: "Order moved to " . $this->order->status->label() . ".");
    }

    public function setStatus(string $status): void
    {
        $target = OrderStatus::from($status);
        $this->order->transitionTo($target, auth()->user());
        $this->refreshOrder();
        $this->dispatch("notify", type: "success", message: "Status updated to {$target->label()}.");
    }

    public function deleteStatusLog(int $logId): void
    {
        $log = $this->order->statusLogs()->find($logId);

        if (! $log) {
            return;
        }

        $log->delete();

        // Roll the order's current status back to whatever the latest
        // remaining log says (or keep it as-is if none are left).
        $latest = $this->order->statusLogs()->latest("created_at")->latest("id")->first();

        if ($latest && ($enum = OrderStatus::tryFrom($latest->status))) {
            $this->order->update([
                "status" => $enum,
                "delivered_at" => $enum === OrderStatus::Delivered ? $this->order->delivered_at : null,
            ]);
        }

        $this->refreshOrder();
        $this->dispatch("notify", type: "success", title: "Entry removed", message: "Status history updated.");
    }

    public function assignRider(): void
    {
        $this->rider_id = $this->rider_id ?: null;
        $this->validate(["rider_id" => "nullable|exists:riders,id"]);
        $this->order->update(["rider_id" => $this->rider_id]);
        $this->refreshOrder();
        $this->dispatch("notify", type: "success", message: $this->rider_id ? "Rider assigned." : "Rider unassigned.");
    }

    public function addPayment(): void
    {
        $data = $this->validate([
            "paymentAmount" => "required|numeric|min:0.01|max:" . max(0.01, $this->order->outstanding),
            "paymentMethod" => "required|in:cash,upi,card,bank_transfer",
            "paymentReference" => "nullable|string|max:100",
        ], [], ["paymentAmount" => "amount", "paymentMethod" => "method"]);

        DB::transaction(function () use ($data) {
            Payment::create([
                "order_id" => $this->order->id,
                "customer_id" => $this->order->customer_id,
                "received_by" => auth()->id(),
                "method" => $data["paymentMethod"],
                "type" => "payment",
                "amount" => $data["paymentAmount"],
                "reference" => $data["paymentReference"] ?: null,
            ]);

            $paid = (float) $this->order->paid_amount + (float) $data["paymentAmount"];
            $this->order->update([
                "paid_amount" => $paid,
                "payment_status" => $paid >= (float) $this->order->total ? "paid" : "partial",
            ]);
        });

        $this->reset(["paymentAmount", "paymentReference"]);
        $this->refreshOrder();
        $this->dispatch("notify", type: "success", message: "Payment recorded.");
    }

    public function getWhatsappShareUrlProperty(): string
    {
        $invoice = $this->order->invoice;
        $lines = [
            "Hi {$this->order->customer?->name}! 🧺",
            "Your Laundrix order *{$this->order->order_no}* — " . $this->order->status->label() . ".",
            $invoice ? "Invoice {$invoice->invoice_no}: ₹" . number_format((float) $this->order->total, 2) : "Total: ₹" . number_format((float) $this->order->total, 2),
            $this->order->outstanding > 0 ? "Balance due: ₹" . number_format($this->order->outstanding, 2) : "Fully paid ✅",
            $invoice ? "Track live: " . $invoice->trackingUrl() : null,
        ];
        $phone = "91" . substr(preg_replace("/\D/", "", (string) $this->order->customer?->mobile), -10);

        return "https://wa.me/{$phone}?text=" . rawurlencode(implode("\n", array_filter($lines)));
    }

    public function sendInvoiceViaApi(\App\Services\WhatsApp\WhatsAppService $whatsapp): void
    {
        if (! config("services.whatsapp.token")) {
            $this->dispatch("notify", type: "error", title: "WhatsApp API not configured",
                message: "Add WHATSAPP_TOKEN and WHATSAPP_PHONE_NUMBER_ID to your .env, or use the Share button instead.");
            return;
        }

        try {
            $invoice = $this->order->invoice;
            $message = "Hi {$this->order->customer?->name}! Your Laundrix order {$this->order->order_no} is " .
                $this->order->status->label() . ". Total ₹" . number_format((float) $this->order->total, 2) .
                ($this->order->outstanding > 0 ? " (balance ₹" . number_format($this->order->outstanding, 2) . ")" : " — fully paid ✅") .
                ($invoice ? ". Track live: " . $invoice->trackingUrl() : "");

            $ok = $whatsapp->sendText((string) $this->order->customer?->mobile, $message);

            $ok
                ? $this->dispatch("notify", type: "success", title: "Sent on WhatsApp", message: "Invoice details delivered to {$this->order->customer?->mobile}.")
                : $this->dispatch("notify", type: "error", title: "WhatsApp rejected the message", message: "Check the number is on WhatsApp and your template/token are valid.");
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch("notify", type: "error", title: "WhatsApp send failed", message: $e->getMessage());
        }
    }

    public function render()
    {
        return view("livewire.admin.orders.show", [
            "pipeline" => OrderStatus::pipeline(),
            "riders" => Rider::with("user")
                ->when($this->order->branch_id, fn ($q) => $q->where("branch_id", $this->order->branch_id))
                ->get(),
        ])->layout("layouts.admin", ["title" => "Order " . $this->order->order_no]);
    }
}
