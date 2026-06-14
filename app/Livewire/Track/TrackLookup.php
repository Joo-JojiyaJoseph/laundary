<?php

namespace App\Livewire\Track;

use App\Models\Order;
use Livewire\Component;

/**
 * Public "Track my order" lookup: invoice number + registered mobile.
 * On a match we redirect to the secure tokenised tracking page.
 */
class TrackLookup extends Component
{
    public string $orderNo = "";
    public string $mobile = "";

    public function track()
    {
        $this->validate([
            "orderNo" => "required|string|max:30",
            "mobile" => "required|string|min:8|max:15",
        ], [], ["orderNo" => "order number", "mobile" => "mobile number"]);

        $order = Order::with(["customer", "invoice"])
            ->whereRaw("UPPER(order_no) = ?", [strtoupper(trim($this->orderNo))])
            ->first();

        $digits = preg_replace("/\D/", "", $this->mobile);
        $matches = $order && $order->customer
            && str_ends_with(preg_replace("/\D/", "", (string) $order->customer->mobile), substr($digits, -10));

        if (! $matches) {
            $this->addError("orderNo", "We couldn't find an order for that order number and mobile combination.");
            return;
        }

        return $this->redirect($order->trackingUrl(), navigate: false);
    }

    public function render()
    {
        return view("livewire.track.track-lookup")
            ->layout("layouts.public", ["title" => "Track your order — Laundrix"]);
    }
}
