<?php

namespace App\Services\AI;

use App\Models\Customer;
use App\Models\Order;

/**
 * Customer-facing AI assistant: answers "where is my order",
 * delivery ETAs, price estimates, pickup requests.
 */
class AiAssistantService
{
    public function __construct(protected OpenAiClient $ai)
    {
    }

    public function answer(Customer $customer, string $question): string
    {
        $orders = $customer->orders()->latest()->take(5)->with("items")->get()
            ->map(fn (Order $o) => [
                "order_no" => $o->order_no,
                "status" => $o->status->label(),
                "total" => (float) $o->total,
                "expected_delivery" => $o->delivery_expected_at?->toDayDateTimeString(),
                "items" => $o->items->pluck("name"),
            ]);

        $reply = trim($this->ai->chat([
            ["role" => "system", "content" =>
                "You are Laundrix Assistant, a concise, friendly helper for a laundry service customer. " .
                "Answer ONLY using the order data provided. If asked for a pickup, tell them you have noted " .
                "the request and the branch will confirm. Keep answers under 80 words.\n\nCustomer: {$customer->name}\n" .
                "Orders: " . $orders->toJson()],
            ["role" => "user", "content" => $question],
        ], ["max_tokens" => 300]));

        return $reply !== "" ? $reply : "Sorry, I could not process that right now.";
    }
}
