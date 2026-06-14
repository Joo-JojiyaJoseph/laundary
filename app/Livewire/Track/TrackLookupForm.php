<?php

namespace App\Livewire\Track;

use App\Models\Order;
use Livewire\Component;

/** Slim, embeddable track-order form (used on the one-page site). */
class TrackLookupForm extends Component
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
        $matches = $order && $order->customer && strlen($digits) >= 8
            && str_ends_with(preg_replace("/\D/", "", (string) $order->customer->mobile), substr($digits, -10));

        if (! $matches) {
            $this->addError("orderNo", "No order found for that order number and mobile combination. Check the bill and try again.");
            return;
        }

        return $this->redirect($order->trackingUrl(), navigate: false);
    }

    public function render()
    {
        return view("livewire.track.track-lookup-form");
    }
}
