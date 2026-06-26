<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Fills the platform with realistic Kerala-flavoured demo data so every
 * admin screen (dashboard, orders, payments, customers, riders) is alive.
 *
 *   php artisan db:seed --class=DemoDataSeeder
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::first() ?? Branch::create(["name" => "Laundrix Kochi HQ", "code" => "BR-HQ01", "city" => "Kochi", "is_active" => true]);
        $products = Product::where("is_active", true)->get();

        if ($products->isEmpty()) {
            $this->command?->warn("Run the main DatabaseSeeder first (services/products missing).");
            return;
        }

        // ── Customers ───────────────────────────────────────────────
        $names = [
            ["Anjali Menon", "Kochi"], ["Rahul Nair", "Muvattupuzha"], ["Fathima Beevi", "Aluva"],
            ["Joseph Varghese", "Thodupuzha"], ["Sreelakshmi Pillai", "Kothamangalam"], ["Arun Krishnan", "Perumbavoor"],
            ["Meera Thomas", "Kochi"], ["Niyas Ahammed", "Kakkanad"], ["Divya Suresh", "Tripunithura"],
            ["George Kutty", "Pala"], ["Lakshmi Warrier", "Angamaly"], ["Vishnu Prasad", "Ernakulam"],
        ];
        $customers = collect($names)->map(fn ($n, $i) => Customer::firstOrCreate(
            ["mobile" => "98470" . str_pad((string) (10000 + $i), 5, "0", STR_PAD_LEFT)],
            ["name" => $n[0], "city" => $n[1], "branch_id" => $branch->id,
             "email" => str()->slug($n[0], ".") . "@example.com", "is_vip" => $i % 6 === 0]
        ));

        // ── Riders ─────────────────────────────────────────────────
        foreach ([["Arun Rider", "KL-07-AB-1234"], ["Shibu Rider", "KL-17-CD-5678"]] as $i => [$name, $vehicle]) {
            $email = str()->slug($name, ".") . "@laundrix.ai";
            $user = User::firstOrCreate(["email" => $email], [
                "name" => $name, "password" => Hash::make("password"), "branch_id" => $branch->id,
            ]);
            if (! $user->hasRole("rider")) $user->assignRole("rider");
            Rider::firstOrCreate(["user_id" => $user->id], [
                "branch_id" => $branch->id, "vehicle_number" => $vehicle, "is_online" => $i === 0,
            ]);
        }
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $riders = Rider::all();
        $staff = User::role("super-admin")->first() ?? User::first();
        $pipeline = OrderStatus::pipeline();

        // ── 35 days of orders ──────────────────────────────────────
        foreach (range(34, 0) as $daysAgo) {
            $date = now()->subDays($daysAgo);
            $count = $daysAgo === 0 ? rand(4, 6) : rand(2, 6);

            foreach (range(1, $count) as $n) {
                $customer = $customers->random();
                $createdAt = $date->copy()->setTime(rand(8, 19), rand(0, 59));

                $order = new Order([
                    "branch_id" => $branch->id,
                    "customer_id" => $customer->id,
                    "rider_id" => $riders->isNotEmpty() && rand(0, 1) ? $riders->random()->id : null,
                    "status" => OrderStatus::PickupScheduled,
                    "notes" => null,
                ]);
                $order->created_at = $createdAt;
                $order->save();

                // Items
                $subtotal = 0;
                foreach ($products->random(rand(1, 4)) as $product) {
                    $qty = rand(1, 3);
                    $line = $order->items()->create([
                        "product_id" => $product->id,
                        "service_id" => $product->service_id,
                        "name" => $product->name,
                        "uom" => $product->uom,
                        "qty" => $qty,
                        "unit_price" => $product->price,
                        "line_total" => round($product->price * $qty, 2),
                    ]);
                    $subtotal += (float) $line->line_total;
                }

                $discount = rand(0, 4) === 0 ? round($subtotal * 0.1, 2) : 0;
                $total = round($subtotal - $discount, 2);

                // Status: older orders progress further; today's stay early/mid pipeline
                $maxIndex = $daysAgo >= 3 ? count($pipeline) - 1 : ($daysAgo >= 1 ? rand(3, 7) : rand(0, 4));
                $statusIndex = $daysAgo >= 5 ? count($pipeline) - 1 : rand(min(2, $maxIndex), $maxIndex);
                $status = $pipeline[$statusIndex];

                // Payment: delivered → paid; otherwise advance / unpaid mix
                $paid = $status === OrderStatus::Delivered ? $total : [0, 0, round($total * 0.4, 2)][rand(0, 2)];

                $order->forceFill([
                    "subtotal" => $subtotal,
                    "discount" => $discount,
                    "tax" => 0,
                    "total" => $total,
                    "paid_amount" => $paid,
                    "payment_status" => $paid <= 0 ? "unpaid" : ($paid >= $total ? "paid" : "partial"),
                    "status" => $status,
                    "delivered_at" => $status === OrderStatus::Delivered ? $createdAt->copy()->addHours(rand(20, 60)) : null,
                    "delivery_expected_at" => $createdAt->copy()->addDays(2)->setTime(18, 0),
                ])->save();

                // Status log trail up to current stage
                foreach (array_slice($pipeline, 0, $statusIndex + 1) as $step => $stage) {
                    $log = $order->statusLogs()->create([
                        "status" => $stage->value,
                        "changed_by" => $staff?->id,
                    ]);
                    $log->created_at = $createdAt->copy()->addHours($step * 5);
                    $log->save();
                }

                // Invoice + payment rows
                Invoice::firstOrCreate(["order_id" => $order->id], [
                    "customer_id" => $customer->id, "amount" => $total,
                ]);

                if ($paid > 0) {
                    $payment = new Payment([
                        "order_id" => $order->id,
                        "customer_id" => $customer->id,
                        "received_by" => $staff?->id,
                        "method" => collect(["cash", "upi", "upi", "card", "razorpay"])->random(),
                        "type" => $paid >= $total ? "payment" : "advance",
                        "amount" => $paid,
                    ]);
                    $payment->created_at = $status === OrderStatus::Delivered ? ($order->delivered_at ?? $createdAt) : $createdAt;
                    $payment->save();
                }

                // Loyalty points for paid orders
                if ($paid >= $total && method_exists($customer, "addPoints")) {
                    rescue(fn () => $customer->addPoints((int) floor($total / 10), "Order {$order->order_no}"), report: false);
                }
            }
        }

        // ── Verified customer feedback (shown on the public site) ──────
        $reviews = [
            ["Anjali Menon", "9847012345", 5, "Picked up, washed and delivered next day — my kasavu saree came back spotless. Love the live tracking!"],
            ["Rahul Nair", "9847022345", 5, "Best laundry service in Ernakulam. The steam ironing is crisp and the staff are super friendly."],
            ["Fathima K", "9847032345", 4, "Very convenient doorstep pickup. Slightly pricey but the quality is worth it."],
            ["Joseph Thomas", "9847042345", 5, "Tracked my order from pickup to delivery on my phone. Felt premium. Highly recommend."],
            ["Lakshmi R", "9847052345", 5, "They removed a curry stain I thought was permanent. Brilliant work!"],
        ];
        foreach ($reviews as [$name, $mobile, $rating, $message]) {
            \App\Models\Feedback::firstOrCreate(
                ["mobile" => $mobile, "message" => $message],
                ["name" => $name, "rating" => $rating, "is_verified" => true, "is_approved" => true, "verified_at" => now()->subDays(random_int(1, 30))]
            );
        }

        $this->command?->info("Demo data ready: " . Order::count() . " orders, " . Payment::count() . " payments, " . Customer::count() . " customers.");
    }
}
