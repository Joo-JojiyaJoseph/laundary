<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Pos\Terminal;
use App\Livewire\Public\Home;
use App\Livewire\Public\HomePage;
use App\Livewire\Track\OrderTracker;
use Illuminate\Support\Facades\Route;

// ── Public website ────────────────────────────────────────────────
Route::get("/", HomePage::class)->name("home");

Route::get("/home", Home::class)->name("home.test");

// Single-page site: old URLs land on their section
Route::get("/about", fn () => redirect("/#about"))->name("about");
Route::get("/services", fn () => redirect("/#services"))->name("services");
Route::get("/contact", fn () => redirect("/#contact"))->name("contact");

// ── QR invoice tracking (no login, token-gated) ──────────────────
Route::get("/track", App\Livewire\Track\TrackLookup::class)->name("track.lookup");
Route::get("/track/{orderNo}", OrderTracker::class)->name("track");

// ── Admin / staff (Spatie roles) ──────────────────────────────────
Route::middleware(["auth", "staff:super-admin|admin|branch-manager|counter-staff"])
    ->prefix("admin")->name("admin.")->group(function () {
        Route::get("/dashboard", Dashboard::class)->name("dashboard");
        Route::get("/pos", Terminal::class)->name("pos");

        Route::get("/orders", App\Livewire\Admin\Orders\Index::class)->name("orders.index");
        Route::get("/orders/{order}", App\Livewire\Admin\Orders\Show::class)->name("orders.show");
        Route::get("/payments", App\Livewire\Admin\Payments\Index::class)->name("payments.index");
        Route::get("/customers", App\Livewire\Admin\Customers\Index::class)->name("customers.index");
        Route::get("/customers/{customer}", App\Livewire\Admin\Customers\Show::class)->name("customers.show");
        Route::get("/feedback", App\Livewire\Admin\Feedback\Index::class)->name("feedback.index");

        Route::get("/reports/orders", App\Livewire\Admin\Reports\Orders::class)->name("reports.orders");
        Route::get("/reports/payments", App\Livewire\Admin\Reports\Payments::class)->name("reports.payments");
        Route::get("/services", App\Livewire\Admin\Services\Index::class)->name("services.index");
        Route::get("/categories", App\Livewire\Admin\Categories\Index::class)->name("categories.index");
        Route::get("/items", App\Livewire\Admin\Products\Index::class)->name("products.index");
        Route::get("/price-lists", App\Livewire\Admin\PriceLists\Index::class)->name("price-lists.index");
        Route::get("/riders", App\Livewire\Admin\Riders\Index::class)->name("riders.index");

        Route::middleware("role:super-admin|admin")->group(function () {
            Route::get("/branches", App\Livewire\Admin\Branches\Index::class)->name("branches.index");
        });
    });

// ── Rider app ─────────────────────────────────────────────────────
Route::middleware(["auth", "rider"])->prefix("rider")->name("rider.")->group(function () {
    Route::get("/", App\Livewire\Rider\Board::class)->name("board");
});

// ── Auth (minimal scaffold; swap in Breeze/Jetstream/Fortify as needed) ──
Route::get("/login", function () {
    return view("auth.login");
})->middleware("guest")->name("login");

Route::post("/login", function (Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        "email" => ["required", "email"],
        "password" => ["required"],
    ]);

    if (Illuminate\Support\Facades\Auth::attempt($credentials, $request->boolean("remember"))) {
        $request->session()->regenerate();

        $request->user()->loginHistory()->create([
            "ip_address" => $request->ip(),
            "user_agent" => substr((string) $request->userAgent(), 0, 500),
            "logged_in_at" => now(),
        ]);

        $u = $request->user();
        $isRider = $u->hasRole("rider") || $u->roles()->where("name", "rider")->exists() || $u->rider()->exists();

        // Riders ALWAYS land on their board — never honour an intended admin URL,
        // otherwise visiting /admin/* before login bounces them there and 403s.
        if ($isRider) {
            $request->session()->forget("url.intended");
            return redirect()->route("rider.board");
        }

        return redirect()->intended(route("admin.dashboard"));
    }

    return back()->withErrors(["email" => "These credentials do not match our records."])->onlyInput("email");
})->middleware(["guest", "throttle:5,1"]);

Route::post("/logout", function (Illuminate\Http\Request $request) {
    Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route("home");
})->middleware("auth")->name("logout");
