<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ChatMessage $message) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("chat.{$this->message->chat_conversation_id}")];
    }

    public function broadcastAs(): string { return "chat.message"; }

    /** Only broadcast when a realtime driver is configured. */
    public function broadcastWhen(): bool
    {
        return in_array(config("broadcasting.default"), ["pusher", "reverb", "ably"], true);
    }
}
