<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CheckoutRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Livewire\Pos\Terminal -> PosController
 *
 * The Livewire component kept the cart as server-side session state
 * ($this->cart, mutated across many round trips: addToCart, updateQty,
 * increment, decrement...). A REST client has no persistent Livewire
 * component, so the mobile app owns the cart locally and only sends the
 * final line items once, on checkout(). Everything else becomes read-only
 * lookup endpoints:
 *
 *   render()'s "customers" (typeahead)      -> handled by CustomerController::search()
 *   render()'s "categories"/"services"/"products" -> catalog()
 *   quickAddCustomer()                       -> handled by CustomerController::store()
 *   getSubtotalProperty()/getTotalProperty() -> priceCart() (client can call this to get
 *                                                live totals as the cart changes, or compute
 *                                                locally using the same formula)
 *   checkout()                               -> checkout()
 */
class PosController extends Controller
{
    use ApiResponse;

    /** GET /pos/catalog?category_id=&service_id=&search= — product picker grid. */
    public function catalog()
    {
        $categories = ProductCategory::orderBy('priority')->orderBy('name')->get();

        $services = Service::active()
            ->when(request('category_id'), fn ($q) => $q->where('product_category_id', request('category_id')))
            ->get();

        $products = Product::with('service')->where('is_active', true)
            ->when(request('category_id'), fn ($q) => $q->where('product_category_id', request('category_id')))
            ->when(request('service_id'), fn ($q) => $q->where('service_id', request('service_id')))
            ->when(request('search'), fn ($q) => $q->where('name', 'like', '%' . request('search') . '%'))
            ->orderBy('priority')->take(24)->get();

        return $this->ok(compact('categories', 'services', 'products'), 'Catalog fetched successfully');
    }

    /**
     * POST /pos/price-cart  { customer_id, cart: [{product_id, qty}] }
     * Returns branch/customer-aware unit prices via Product::priceFor(), the
     * same call the Livewire cart used on addToCart(). Lets the mobile app
     * show accurate line totals before checkout without duplicating pricing
     * rules client-side.
     */
    public function priceCart()
    {
        $data = $this->validateOrFail(request()->all(), [
            'customer_id' => 'nullable|exists:customers,id',
            'cart' => 'required|array|min:1',
            'cart.*.product_id' => 'required|exists:products,id',
            'cart.*.qty' => 'required|numeric|min:0.01',
        ]);

        $customer = ($data['customer_id'] ?? null) ? Customer::find($data['customer_id']) : null;
        $branch = auth()->user()->branch;

        $lines = [];
        $subtotal = 0.0;

        foreach ($data['cart'] as $line) {
            $product = Product::with('service')->find($line['product_id']);
            $price = $product->priceFor($branch, $customer);
            $qty = (float) $line['qty'];

            $lines[] = [
                'product_id' => $product->id,
                'service_id' => $product->service_id,
                'name' => "{$product->name} \u{00B7} {$product->service->name}",
                'uom' => $product->uom,
                'price' => $price,
                'qty' => $qty,
                'line_total' => round($price * $qty, 2),
            ];
            $subtotal += $price * $qty;
        }

        return $this->ok([
            'lines' => $lines,
            'subtotal' => round($subtotal, 2),
        ], 'Cart priced successfully');
    }

    /** POST /pos/checkout — creates the order, items, optional advance payment and invoice. */
    public function checkout(CheckoutRequest $request)
    {
        $data = $request->validated();
        $customer = Customer::find($data['customer_id']);
        $branch = auth()->user()->branch;

        // Re-resolve unit price server-side for every line (never trust client prices).
        $lines = [];
        $subtotal = 0.0;

        foreach ($data['cart'] as $line) {
            $product = Product::with('service')->find($line['product_id']);
            $price = $product->priceFor($branch, $customer);
            $qty = (float) $line['qty'];

            $lines[] = [
                'product_id' => $product->id,
                'service_id' => $product->service_id,
                'name' => "{$product->name} \u{00B7} {$product->service->name}",
                'uom' => $product->uom,
                'unit_price' => $price,
                'qty' => $qty,
                'line_total' => round($price * $qty, 2),
            ];
            $subtotal += $price * $qty;
        }

        $discount = (float) ($data['discount'] ?? 0);
        $advance = (float) ($data['advance'] ?? 0);
        $total = round(max(0, $subtotal - $discount), 2);
        $paymentMethod = $data['payment_method'] ?? 'cash';

        try {
            $order = DB::transaction(function () use ($data, $lines, $subtotal, $discount, $total, $advance, $paymentMethod) {
                $order = Order::create([
                    'pickup_at' => Carbon::parse($data['pickup_date'])->setTime(10, 0),
                    'delivery_expected_at' => Carbon::parse("{$data['delivery_date']} {$data['delivery_time']}"),
                    'delivery_address' => $data['delivery_address'] ?? null,
                    'branch_id' => auth()->user()->branch_id,
                    'customer_id' => $data['customer_id'],
                    'created_by' => auth()->id(),
                    'subtotal' => round($subtotal, 2),
                    'discount' => $discount,
                    'tax' => 0,
                    'total' => $total,
                    'paid_amount' => min($advance, $total),
                    'payment_status' => $advance >= $total ? 'paid' : ($advance > 0 ? 'partial' : 'unpaid'),
                    'notes' => $data['notes'] ?? null,
                ]);

                foreach ($lines as $line) {
                    $order->items()->create($line);
                }

                if ($advance > 0) {
                    $order->payments()->create([
                        'customer_id' => $data['customer_id'],
                        'received_by' => auth()->id(),
                        'method' => $paymentMethod,
                        'type' => 'advance',
                        'amount' => min($advance, $total),
                    ]);
                }

                Invoice::create(['order_id' => $order->id, 'customer_id' => $data['customer_id'], 'amount' => $total]);
                $order->transitionTo(OrderStatus::PickupScheduled, auth()->user(), 'Order created via mobile POS API');

                return $order;
            });
        } catch (\Throwable $e) {
            report($e);

            return $this->fail('Checkout failed: ' . $e->getMessage(), 500);
        }

        $order->load(['customer', 'branch', 'items', 'invoice']);

        return $this->created($order, "Order {$order->order_no} created.");
    }
}
