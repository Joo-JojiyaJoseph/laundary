<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TrackLookupRequest;
use App\Models\Order;

/**
 * Livewire\Track\OrderTracker + TrackLookup + TrackLookupForm -> TrackController
 * Public, unauthenticated endpoints — no "auth:sanctum" middleware.
 *
 *   TrackLookup::track() / TrackLookupForm::track() (identical logic,
 *     duplicated only because one was a full page and one an embeddable
 *     widget) -> lookup() below, single implementation.
 *   OrderTracker::mount() (token gate) -> show()
 *   OrderTracker::render()             -> show()
 */
class TrackController extends Controller
{
    use ApiResponse;

    /** POST /track/lookup  { order_no, mobile } -> resolves to the tracking URL/token. */
    public function lookup(TrackLookupRequest $request)
    {
        $data = $request->validated();

        $order = Order::with(['customer', 'invoice'])
            ->whereRaw('UPPER(order_no) = ?', [strtoupper(trim($data['order_no']))])
            ->first();

        $digits = preg_replace('/\D/', '', $data['mobile']);
        $matches = $order && $order->customer
            && str_ends_with(preg_replace('/\D/', '', (string) $order->customer->mobile), substr($digits, -10));

        if (! $matches) {
            return $this->fail("We couldn't find an order for that order number and mobile combination.", 404, [
                'order_no' => ["We couldn't find an order for that order number and mobile combination."],
            ]);
        }

        return $this->ok([
            'order_no' => $order->order_no,
            'tracking_url' => $order->trackingUrl(),
            'tracking_token' => $order->invoice?->tracking_token,
        ], 'Order found');
    }

    /** GET /track/{orderNo}?t={token} — same token gate as OrderTracker::mount(). */
    public function show(string $orderNo)
    {
        $order = Order::where('order_no', $orderNo)->with('invoice')->firstOrFail();

        $token = $order->invoice?->tracking_token;
        abort_unless($token && hash_equals($token, (string) request('t')), 403);

        $order->load(['items', 'statusLogs', 'branch']);

        return $this->ok([
            'order' => $order,
            'pipeline' => OrderStatus::pipeline(),
            'reached' => $order->statusLogs->pluck('created_at', 'status'),
        ], 'Order tracking info fetched successfully');
    }
}
