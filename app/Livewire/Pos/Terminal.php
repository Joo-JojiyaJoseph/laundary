<?php

namespace App\Livewire\Pos;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Terminal extends Component
{
    public string $customerSearch = "";
    public ?int $customerId = null;
    public string $productSearch = "";
    public $serviceFilter = null;

    /** @var array<int, array{product_id:int,name:string,uom:string,price:float,qty:float}> */
    public array $cart = [];

    public $discount = 0;
    public $taxPercent = 0;
    public $advance = 0;
    public string $paymentMethod = "cash";
    public string $notes = "";

    // Pickup & delivery schedule
    public $pickupDate = null;
    public $deliveryDate = null;
    public $deliveryTime = "18:00";
    public $deliveryAddress = "";

    // Quick customer creation
    public bool $showQuickAdd = false;
    public string $newName = "";
    public string $newMobile = "";

    public function mount(): void
    {
        $this->pickupDate = now()->toDateString();
        $this->deliveryDate = now()->addDays(2)->toDateString();
    }

    public function quickAddCustomer(): void
    {
        $this->validate([
            "newName" => "required|string|min:2|max:120",
            "newMobile" => "required|string|min:8|max:15|unique:customers,mobile",
        ], [], ["newName" => "name", "newMobile" => "mobile"]);

        $customer = \App\Models\Customer::create([
            "name" => $this->newName,
            "mobile" => $this->newMobile,
            "branch_id" => auth()->user()->branch_id,
        ]);

        $this->customerId = $customer->id;
        $this->reset(["newName", "newMobile", "showQuickAdd", "customerSearch"]);
        $this->dispatch("notify", type: "success", title: "Customer added", message: "{$customer->name} is selected for this order.");
    }

    public function selectCustomer(int $id): void
    {
        $this->customerId = $id;
        $this->customerSearch = "";
    }

    public function addToCart(int $productId): void
    {
        $product = Product::findOrFail($productId);
        $customer = $this->customerId ? Customer::find($this->customerId) : null;

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]["qty"]++;
        } else {
            $this->cart[$productId] = [
                "product_id" => $productId,
                "service_id" => $product->service_id,
                "name" => "{$product->name} · {$product->service->name}",
                "uom" => $product->uom,
                "price" => $product->priceFor(auth()->user()->branch, $customer),
                "qty" => 1.0,
            ];
        }
    }

    public function updateQty(int $productId, float $qty): void
    {
        if ($qty <= 0) { unset($this->cart[$productId]); return; }
        $this->cart[$productId]["qty"] = $qty;
    }

    public function increment(int $productId): void
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]["qty"]++;
        }
    }

    public function decrement(int $productId): void
    {
        if (isset($this->cart[$productId])) {
            $this->updateQty($productId, (float) $this->cart[$productId]["qty"] - 1);
        }
    }

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
    }

    public function clearCustomer(): void
    {
        $this->customerId = null;
    }

    public function getTaxAmountProperty(): float
    {
        return 0.0;
    }

    public function getOutstandingProperty(): float
    {
        return max(0, round($this->total - (float) $this->advance, 2));
    }

    public function getSubtotalProperty(): float
    {
        return (float) collect($this->cart)->sum(fn ($i) => (float) $i["price"] * (float) $i["qty"]);
    }

    public function getTotalProperty(): float
    {
        return round(max(0, $this->subtotal - (float) $this->discount), 2);
    }

    public function checkout(): void
    {
        $this->deliveryAddress = trim((string) $this->deliveryAddress) ?: null;

        $this->validate([
            "customerId" => "required|exists:customers,id",
            "cart" => "required|array|min:1",
            "pickupDate" => "required|date",
            "deliveryDate" => "required|date|after_or_equal:pickupDate",
            "deliveryTime" => "required|date_format:H:i",
            "deliveryAddress" => "nullable|string|max:500",
        ], ["customerId.required" => "Select a customer first.", "cart.required" => "Cart is empty."],
           ["pickupDate" => "pickup date", "deliveryDate" => "delivery date", "deliveryTime" => "delivery time"]);

        try {
            $order = DB::transaction(function () {
            $order = Order::create([
                "pickup_at" => \Illuminate\Support\Carbon::parse($this->pickupDate)->setTime(10, 0),
                "delivery_expected_at" => \Illuminate\Support\Carbon::parse("{$this->deliveryDate} {$this->deliveryTime}"),
                "delivery_address" => $this->deliveryAddress,
                "branch_id" => auth()->user()->branch_id,
                "customer_id" => $this->customerId,
                "created_by" => auth()->id(),
                "subtotal" => $this->subtotal,
                "discount" => $this->discount,
                "tax" => 0,
                "total" => $this->total,
                "paid_amount" => min($this->advance, $this->total),
                "payment_status" => $this->advance >= $this->total ? "paid" : ($this->advance > 0 ? "partial" : "unpaid"),
                "notes" => $this->notes ?: null,
            ]);

            foreach ($this->cart as $line) {
                $order->items()->create([
                    "product_id" => $line["product_id"],
                    "service_id" => $line["service_id"],
                    "name" => $line["name"],
                    "qty" => $line["qty"],
                    "uom" => $line["uom"],
                    "unit_price" => $line["price"],
                    "line_total" => round($line["price"] * $line["qty"], 2),
                ]);
            }

            if ($this->advance > 0) {
                $order->payments()->create([
                    "customer_id" => $this->customerId,
                    "received_by" => auth()->id(),
                    "method" => $this->paymentMethod,
                    "type" => "advance",
                    "amount" => min($this->advance, $this->total),
                ]);
            }

            Invoice::create(["order_id" => $order->id, "customer_id" => $this->customerId, "amount" => $this->total]);
            $order->transitionTo(OrderStatus::PickupScheduled, auth()->user(), "Order created at POS");

            return $order;
        });

        $this->reset(["cart", "customerId", "discount", "advance", "notes", "deliveryAddress"]);
        session()->flash("success", "Order {$order->order_no} created.");

        $this->redirectRoute("admin.orders.show", $order, navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch("notify", type: "error", title: "Checkout failed", message: $e->getMessage());
        }
    }

    public function render()
    {
        return view("livewire.pos.terminal", [
            "customers" => strlen($this->customerSearch) >= 2
                ? Customer::where(fn ($q) => $q->where("name", "like", "%{$this->customerSearch}%")
                    ->orWhere("mobile", "like", "%{$this->customerSearch}%"))->take(6)->get()
                : collect(),
            "customer" => $this->customerId ? Customer::find($this->customerId) : null,
            "services" => Service::active()->get(),
            "products" => Product::with("service")->where("is_active", true)
                ->when($this->serviceFilter, fn ($q) => $q->where("service_id", $this->serviceFilter))
                ->when($this->productSearch, fn ($q) => $q->where("name", "like", "%{$this->productSearch}%"))
                ->orderBy("priority")->take(24)->get(),
        ])->layout("layouts.admin", ["title" => "POS"]);
    }
}
