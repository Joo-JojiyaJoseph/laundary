<?php

namespace App\Events;

use App\Models\Rider;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class RiderLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public Rider $rider) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("riders.{$this->rider->id}.location")];
    }

    public function broadcastAs(): string { return "rider.location"; }

    public function broadcastWith(): array
    {
        return [
            "rider_id" => $this->rider->id,
            "lat" => (float) $this->rider->current_lat,
            "lng" => (float) $this->rider->current_lng,
            "at"  => $this->rider->location_updated_at?->toIso8601String(),
        ];
    }

    /** Only broadcast when a realtime driver is configured. */
    public function broadcastWhen(): bool
    {
        return in_array(config("broadcasting.default"), ["pusher", "reverb", "ably"], true);
    }
}
