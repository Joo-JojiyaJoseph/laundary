<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * Livewire\Rider\Board -> RiderBoardController (mobile app's main screen for riders)
 *   tab (pickups|deliveries|done)         -> board() returns all three lists at once
 *                                             (cheap for a rider's small daily list; avoids
 *                                             3 separate round trips on a mobile connection)
 *   markPickedUp()/startDelivery()        -> markPickedUp()/startDelivery()
 *   markDelivered() (with OTP check)       -> markDelivered()
 *   riderOrder() guard                     -> riderOrder() (ensures the order belongs to
 *                                             the authenticated rider — 403s otherwise)
 */
class RiderBoardController extends Controller
{
    use ApiResponse;

    public function board(Request $request)
    {
        $riderId = $request->user()->rider?->id;
        $base = Order::with(['customer', 'branch'])->where('rider_id', $riderId);

        return $this->ok([
            'pickups' => (clone $base)->where('status', OrderStatus::PickupScheduled)->oldest('pickup_at')->get(),
            'deliveries' => (clone $base)->whereIn('status', [OrderStatus::Ready, OrderStatus::OutForDelivery])->oldest('delivery_expected_at')->get(),
            'done' => (clone $base)->where('status', OrderStatus::Delivered)->whereDate('delivered_at', today())->latest('delivered_at')->get(),
        ], "Rider's board fetched successfully");
    }

    public function markPickedUp(Request $request, int $orderId)
    {
        $order = $this->riderOrder($request, $orderId);
        $order->transitionTo(OrderStatus::PickedUp, $request->user(), 'Picked up by rider');

        return $this->ok($order->fresh(['customer', 'branch']), "{$order->order_no} collected from {$order->customer?->name}.");
    }

    public function startDelivery(Request $request, int $orderId)
    {
        $order = $this->riderOrder($request, $orderId);
        $order->transitionTo(OrderStatus::OutForDelivery, $request->user(), 'Rider on the way');

        return $this->ok($order->fresh(['customer', 'branch']), "On the way to {$order->customer?->name}.");
    }

    /** POST /rider/orders/{order}/mark-delivered  { "otp": "123456" } */
    public function markDelivered(Request $request, int $orderId)
    {
        $order = $this->riderOrder($request, $orderId);
        $entered = trim((string) $request->input('otp', ''));

        if ($order->delivery_otp && ! hash_equals($order->delivery_otp, $entered)) {
            return $this->fail('Wrong OTP — ask the customer for the 6-digit code.', 422, ['otp' => ['Invalid OTP']]);
        }

        $order->transitionTo(OrderStatus::Delivered, $request->user(), 'Delivered with OTP');
        $order->update(['delivered_at' => now()]);

        return $this->ok($order->fresh(['customer', 'branch']), "{$order->order_no} handed over successfully.");
    }

    protected function riderOrder(Request $request, int $orderId): Order
    {
        return Order::where('rider_id', $request->user()->rider?->id)->findOrFail($orderId);
    }
}
