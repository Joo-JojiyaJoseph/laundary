<?php

namespace App\Livewire\Rider;

use App\Enums\OrderStatus;
use App\Models\Order;
use Livewire\Component;

class Board extends Component
{
    public string $tab = "deliveries"; // pickups|deliveries|done
    public array $otp = [];

    public function markPickedUp(int $orderId): void
    {
        $order = $this->riderOrder($orderId);
        $order->transitionTo(OrderStatus::PickedUp, auth()->user(), "Picked up by rider");
        $this->dispatch("notify", type: "success", title: "Picked up!", message: "{$order->order_no} collected from {$order->customer?->name}.");
    }

    public function startDelivery(int $orderId): void
    {
        $order = $this->riderOrder($orderId);
        $order->transitionTo(OrderStatus::OutForDelivery, auth()->user(), "Rider on the way");
        $this->dispatch("notify", type: "success", title: "On the way", message: "Share live location with {$order->customer?->name}.");
    }

    public function markDelivered(int $orderId): void
    {
        $order = $this->riderOrder($orderId);
        $entered = trim((string) ($this->otp[$orderId] ?? ""));

        if ($order->delivery_otp && ! hash_equals($order->delivery_otp, $entered)) {
            $this->addError("otp.{$orderId}", "Wrong OTP — ask the customer for the 6-digit code.");
            return;
        }

        $order->transitionTo(OrderStatus::Delivered, auth()->user(), "Delivered with OTP");
        $order->update(["delivered_at" => now()]);
        unset($this->otp[$orderId]);
        $this->dispatch("notify", type: "success", title: "Delivered 🎉", message: "{$order->order_no} handed over successfully.");
    }

    protected function riderOrder(int $orderId): Order
    {
        return Order::where("rider_id", auth()->user()->rider?->id)->findOrFail($orderId);
    }

    public function render()
    {
        $riderId = auth()->user()->rider?->id;
        $base = Order::with(["customer", "branch"])->where("rider_id", $riderId);

        return view("livewire.rider.board", [
            "pickups" => (clone $base)->where("status", OrderStatus::PickupScheduled)->oldest("pickup_at")->get(),
            "deliveries" => (clone $base)->whereIn("status", [OrderStatus::Ready, OrderStatus::OutForDelivery])->oldest("delivery_expected_at")->get(),
            "done" => (clone $base)->where("status", OrderStatus::Delivered)->whereDate("delivered_at", today())->latest("delivered_at")->get(),
        ])->layout("layouts.rider", ["title" => "My runs"]);
    }
}
