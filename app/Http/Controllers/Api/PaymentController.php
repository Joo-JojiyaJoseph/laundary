<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Api\Concerns\AppliesDateFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Livewire\Admin\Payments\Index -> PaymentController
 *   tab (outstanding|payments)         -> two separate endpoints: index() and outstanding()
 *   search/methodFilter/period/...     -> query params
 *   openModal()/selectOrder()          -> handled client-side + OrderController::show() for order details
 *   orderSearch/"dueOrders"            -> outstandingOrders() with ?search=
 *   recordPayment()                     -> store()
 *
 * Note: the same recordPayment()/addPayment() logic that lived in both
 * Payments\Index and Orders\Show is now unified in store() below, since both
 * Livewire components duplicated the exact same DB::transaction.
 */
class PaymentController extends Controller
{
    use ApiResponse;
    use AppliesDateFilter;

    protected function branchScope($query, $user)
    {
        return $query->when(! $user->hasRole('super-admin') && $user->branch_id,
            fn ($w) => $w->where('branch_id', $user->branch_id));
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $payments = Payment::with(['order', 'customer', 'receivedBy'])
            ->whereHas('order', fn ($q) => $this->branchScope($q, $user))
            ->when($request->query('search'), fn ($q) => $q->where(fn ($w) => $w
                ->whereHas('order', fn ($o) => $o->where('order_no', 'like', '%' . $request->query('search') . '%'))
                ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', '%' . $request->query('search') . '%'))))
            ->when($request->query('method'), fn ($q) => $q->where('method', $request->query('method')))
            ->tap(fn ($q) => $this->applyDateFilter($q))
            ->latest()
            ->paginate((int) $request->query('per_page', 15));

        return $this->ok($payments, 'Payments fetched successfully');
    }

    /** GET /payments/outstanding — orders that still owe money (the "outstanding" tab). */
    public function outstandingOrders(Request $request)
    {
        $user = $request->user();

        $orders = Order::with('customer')
            ->tap(fn ($q) => $this->branchScope($q, $user))
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->whereColumn('paid_amount', '<', 'total')
            ->when($request->query('search'), fn ($q) => $q->where(fn ($w) => $w
                ->where('order_no', 'like', '%' . $request->query('search') . '%')
                ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', '%' . $request->query('search') . '%')
                    ->orWhere('mobile', 'like', '%' . $request->query('search') . '%'))))
            ->latest()
            ->paginate((int) $request->query('per_page', 15));

        return $this->ok($orders, 'Outstanding orders fetched successfully');
    }

    /** GET /payments/summary — today's total + total due, shown as header stats. */
    public function summary(Request $request)
    {
        $user = $request->user();

        $todayTotal = Payment::whereHas('order', fn ($q) => $this->branchScope($q, $user))
            ->whereDate('created_at', today())->sum('amount');

        $dueTotal = Order::tap(fn ($q) => $this->branchScope($q, $user))
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->selectRaw('COALESCE(SUM(total - paid_amount), 0) as due')->value('due');

        return $this->ok(compact('todayTotal', 'dueTotal'), 'Payment summary fetched successfully');
    }

    /**
     * POST /payments — record a payment against an order.
     * Shared logic for both "record payment from Payments tab" and
     * "add payment from an order's detail page".
     */
    public function store(StorePaymentRequest $request)
    {
        $data = $request->validated();
        $order = Order::findOrFail($data['order_id']);

        try {
            $payment = DB::transaction(function () use ($order, $data) {
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'customer_id' => $order->customer_id,
                    'received_by' => auth()->id(),
                    'method' => $data['method'],
                    'type' => 'payment',
                    'amount' => $data['amount'],
                    'reference' => $data['reference'] ?? null,
                ]);

                $paid = (float) $order->paid_amount + (float) $data['amount'];
                $order->update([
                    'paid_amount' => $paid,
                    'payment_status' => $paid >= (float) $order->total ? 'paid' : 'partial',
                ]);

                return $payment;
            });
        } catch (\Throwable $e) {
            report($e);

            return $this->fail('Could not record payment: ' . $e->getMessage(), 500);
        }

        $payment->load(['order', 'customer', 'receivedBy']);

        return $this->created($payment, "\u{20B9}" . number_format((float) $data['amount'], 2) . " received for {$order->order_no}.");
    }
}
