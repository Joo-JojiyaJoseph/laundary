<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Api\Concerns\AppliesDateFilter;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Livewire\Admin\Orders\Index + Livewire\Admin\Orders\Show -> OrderController
 *
 *   Index::render() "orders" query               -> index()
 *   Show::mount()/refreshOrder()                  -> show()
 *   Show::advanceStatus()                          -> advanceStatus()
 *   Show::setStatus()                              -> setStatus()
 *   Show::deleteStatusLog()                        -> deleteStatusLog()
 *   Show::assignRider()                            -> assignRider()
 *   Show::addPayment()                              -> handled by PaymentController::store()
 *                                                      (same DB transaction logic, shared with
 *                                                      Payments\Index::recordPayment())
 *   Show::getWhatsappShareUrlProperty()             -> whatsappShareUrl()
 *   Show::sendInvoiceViaApi()                       -> sendInvoiceWhatsapp()
 *   real-time echo listeners (order.status.updated) -> not applicable over REST; the mobile
 *                                                       app should poll show()/index() or use
 *                                                       Laravel Echo/Pusher directly if it needs push updates.
 *
 * Live-status note: the "echo-private:branches.{id},.order.status.updated" listeners that
 * trigger `$refresh` in Livewire have no REST analogue — polling or a websocket
 * connection (Pusher/Reverb) is the mobile equivalent.
 */
class OrderController extends Controller
{
    use ApiResponse;
    use AppliesDateFilter;

    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::with(['customer', 'branch'])
            ->when(! $user->hasRole('super-admin') && $user->branch_id, fn ($q) => $q->where('branch_id', $user->branch_id))
            ->when($request->query('search'), fn ($q) => $q->where(fn ($w) => $w
                ->where('order_no', 'like', '%' . $request->query('search') . '%')
                ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', '%' . $request->query('search') . '%')
                    ->orWhere('mobile', 'like', '%' . $request->query('search') . '%'))))
            ->when($request->query('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->query('payment_status'), fn ($q) => $q->where('payment_status', $request->query('payment_status')))
            ->tap(fn ($q) => $this->applyDateFilter($q))
            ->latest()
            ->paginate((int) $request->query('per_page', 15));

        return $this->ok($orders, 'Orders fetched successfully');
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'branch', 'rider.user', 'items.product', 'statusLogs', 'payments.receivedBy', 'invoice']);

        return $this->ok([
            'order' => $order,
            'pipeline' => OrderStatus::pipeline(),
            'available_riders' => Rider::with('user')
                ->when($order->branch_id, fn ($q) => $q->where('branch_id', $order->branch_id))
                ->get(),
        ], 'Order fetched successfully');
    }

    /** POST /orders/{order}/advance-status */
    public function advanceStatus(Order $order)
    {
        $pipeline = OrderStatus::pipeline();
        $index = array_search($order->status, $pipeline);

        if ($index === false || ! isset($pipeline[$index + 1])) {
            return $this->fail('Order is already at the final status', 422);
        }

        $order->transitionTo($pipeline[$index + 1], auth()->user());
        $order->refresh()->load(['customer', 'branch', 'rider.user', 'items.product', 'statusLogs', 'payments.receivedBy', 'invoice']);

        return $this->ok($order, 'Order moved to ' . $order->status->label());
    }

    /** POST /orders/{order}/set-status  { "status": "..." } */
    public function setStatus(Request $request, Order $order)
    {
        $data = $this->validateOrFail($request->all(), [
            'status' => ['required', Rule::in(array_map(fn ($c) => $c->value, OrderStatus::cases()))],
        ]);

        $target = OrderStatus::from($data['status']);
        $order->transitionTo($target, auth()->user());
        $order->refresh()->load(['customer', 'branch', 'rider.user', 'items.product', 'statusLogs', 'payments.receivedBy', 'invoice']);

        return $this->ok($order, "Status updated to {$target->label()}.");
    }

    /** DELETE /orders/{order}/status-logs/{log} */
    public function deleteStatusLog(Order $order, int $logId)
    {
        $log = $order->statusLogs()->find($logId);

        if (! $log) {
            return $this->fail('Status log entry not found', 404);
        }

        $log->delete();

        // Roll the order's current status back to whatever the latest remaining log says.
        $latest = $order->statusLogs()->latest('created_at')->latest('id')->first();

        if ($latest && ($enum = OrderStatus::tryFrom($latest->status))) {
            $order->update([
                'status' => $enum,
                'delivered_at' => $enum === OrderStatus::Delivered ? $order->delivered_at : null,
            ]);
        }

        $order->refresh()->load(['customer', 'branch', 'rider.user', 'items.product', 'statusLogs', 'payments.receivedBy', 'invoice']);

        return $this->ok($order, 'Status history updated.');
    }

    /** POST /orders/{order}/assign-rider  { "rider_id": 1|null } */
    public function assignRider(Request $request, Order $order)
    {
        $data = $this->validateOrFail($request->all(), [
            'rider_id' => 'nullable|exists:riders,id',
        ]);

        $order->update(['rider_id' => $data['rider_id'] ?? null]);
        $order->refresh()->load(['customer', 'branch', 'rider.user']);

        return $this->ok($order, $order->rider_id ? 'Rider assigned.' : 'Rider unassigned.');
    }

    /** GET /orders/{order}/whatsapp-share-url */
    public function whatsappShareUrl(Order $order)
    {
        $order->load(['customer', 'invoice']);
        $invoice = $order->invoice;

        $lines = [
            "Hi {$order->customer?->name}! \u{1F9FA}",
            "Your Laundrix order *{$order->order_no}* — " . $order->status->label() . '.',
            $invoice ? "Invoice {$invoice->invoice_no}: \u{20B9}" . number_format((float) $order->total, 2) : "Total: \u{20B9}" . number_format((float) $order->total, 2),
            $order->outstanding > 0 ? "Balance due: \u{20B9}" . number_format($order->outstanding, 2) : "Fully paid \u{2705}",
            $invoice ? 'Track live: ' . $invoice->trackingUrl() : null,
        ];
        $phone = '91' . substr(preg_replace('/\D/', '', (string) $order->customer?->mobile), -10);
        $url = "https://wa.me/{$phone}?text=" . rawurlencode(implode("\n", array_filter($lines)));

        return $this->ok(['url' => $url], 'WhatsApp share URL generated');
    }

    /** POST /orders/{order}/send-invoice-whatsapp */
    public function sendInvoiceWhatsapp(Order $order, \App\Services\WhatsApp\WhatsAppService $whatsapp)
    {
        if (! config('services.whatsapp.token')) {
            return $this->fail('WhatsApp API not configured. Add WHATSAPP_TOKEN and WHATSAPP_PHONE_NUMBER_ID to your .env.', 422);
        }

        $order->load(['customer', 'invoice']);

        try {
            $invoice = $order->invoice;
            $message = "Hi {$order->customer?->name}! Your Laundrix order {$order->order_no} is " .
                $order->status->label() . ". Total \u{20B9}" . number_format((float) $order->total, 2) .
                ($order->outstanding > 0 ? " (balance \u{20B9}" . number_format($order->outstanding, 2) . ')' : " — fully paid \u{2705}") .
                ($invoice ? '. Track live: ' . $invoice->trackingUrl() : '');

            $ok = $whatsapp->sendText((string) $order->customer?->mobile, $message);

            return $ok
                ? $this->ok(null, "Invoice details delivered to {$order->customer?->mobile}.")
                : $this->fail('WhatsApp rejected the message. Check the number is on WhatsApp and your template/token are valid.', 422);
        } catch (\Throwable $e) {
            report($e);

            return $this->fail('WhatsApp send failed: ' . $e->getMessage(), 500);
        }
    }
}
