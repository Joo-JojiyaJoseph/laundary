<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("orders.{$this->order->id}"),            // public QR tracking page
            new PrivateChannel("customers.{$this->order->customer_id}"),
            new PrivateChannel("branches.{$this->order->branch_id}"), // admin dashboard
        ];
    }

    public function broadcastAs(): string { return "order.status.updated"; }

    public function broadcastWith(): array
    {
        return [
            "order_id"  => $this->order->id,
            "order_no"  => $this->order->order_no,
            "status"    => $this->order->status->value,
            "label"     => $this->order->status->label(),
            "timeline"  => $this->order->statusLogs->map(fn ($l) => [
                "status" => $l->status, "at" => $l->created_at->toIso8601String(),
            ]),
            "updated_at" => now()->toIso8601String(),
        ];
    }

    /** Only broadcast when a realtime driver is configured. */
    public function broadcastWhen(): bool
    {
        return in_array(config("broadcasting.default"), ["pusher", "reverb", "ably"], true);
    }
}
