<?php

namespace App\Livewire\Track;

use App\Enums\OrderStatus;
use App\Models\Order;
use Livewire\Component;

/**
 * Public QR-code tracking page: /track/{order_no}?t={token}
 * No login required — access is gated by the per-order secret token
 * (stored on the related invoice).
 */
class OrderTracker extends Component
{
    public Order $order;

    public function mount(string $orderNo)
    {
        $this->order = Order::where("order_no", $orderNo)->with("invoice")->firstOrFail();

        $token = $this->order->invoice?->tracking_token;
        abort_unless($token && hash_equals($token, (string) request("t")), 403);
    }

    public function getListeners(): array
    {
        return ["echo:orders.{$this->order->id},.order.status.updated" => "\$refresh"];
    }

    public function render()
    {
        $order = $this->order->load(["items", "statusLogs", "branch"]);

        return view("livewire.track.order-tracker", [
            "order" => $order,
            "pipeline" => OrderStatus::pipeline(),
            "reached" => $order->statusLogs->pluck("created_at", "status"),
        ])->layout("layouts.track", ["title" => "Track {$order->order_no} — Laundrix"]);
    }
}
