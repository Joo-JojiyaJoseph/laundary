<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PosController;
use App\Http\Controllers\Api\PriceListController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PublicFeedbackController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\RiderBoardController;
use App\Http\Controllers\Api\RiderController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\TrackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — v1
|--------------------------------------------------------------------------
| Mirrors routes/web.php's three areas (public site, admin/staff, rider app)
| but as stateless JSON endpoints for the mobile app, authenticated with
| Sanctum bearer tokens instead of the session-based web guard.
*/

Route::prefix('v1')->group(function () {

    // ── Public, unauthenticated (mirrors the "public website" block in web.php) ──
    Route::post('auth/register', [AuthController::class, 'register']); // 
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::get('public/home', [HomeController::class, 'index']);
    Route::get('public/feedback', [PublicFeedbackController::class, 'index']);
    Route::post('public/feedback', [PublicFeedbackController::class, 'store']);
    Route::post('public/contact', [ContactController::class, 'store']);

    // QR invoice tracking — no login, token-gated (mirrors the "track" block).
    Route::post('track/lookup', [TrackController::class, 'lookup']);
    Route::get('track/{orderNo}', [TrackController::class, 'show']);

    // ── Authenticated (any logged-in user: admin/staff or rider) ──
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/logout-all', [AuthController::class, 'logoutAll']);

        // ── Admin / staff area (mirrors ["auth", "staff:super-admin|admin|branch-manager|counter-staff"]) ──
        Route::middleware('staff:super-admin|admin|branch-manager|counter-staff')->group(function () {
            Route::get('dashboard', [DashboardController::class, 'index']);

            // POS terminal
            Route::get('pos/catalog', [PosController::class, 'catalog']);
            Route::post('pos/price-cart', [PosController::class, 'priceCart']);
            Route::post('pos/checkout', [PosController::class, 'checkout']);

            // Orders
            Route::get('orders', [OrderController::class, 'index']);
            Route::get('orders/{order}', [OrderController::class, 'show']);
            Route::post('orders/{order}/advance-status', [OrderController::class, 'advanceStatus']);
            Route::post('orders/{order}/set-status', [OrderController::class, 'setStatus']);
            Route::delete('orders/{order}/status-logs/{logId}', [OrderController::class, 'deleteStatusLog']);
            Route::post('orders/{order}/assign-rider', [OrderController::class, 'assignRider']);
            Route::get('orders/{order}/whatsapp-share-url', [OrderController::class, 'whatsappShareUrl']);
            Route::post('orders/{order}/send-invoice-whatsapp', [OrderController::class, 'sendInvoiceWhatsapp']);

            // Payments
            Route::get('payments', [PaymentController::class, 'index']);
            Route::get('payments/outstanding', [PaymentController::class, 'outstandingOrders']);
            Route::get('payments/summary', [PaymentController::class, 'summary']);
            Route::post('payments', [PaymentController::class, 'store']);

            // Customers
            Route::get('customers/search', [CustomerController::class, 'search']);
            Route::get('customers/{customer}/summary', [CustomerController::class, 'summary']);
            Route::apiResource('customers', CustomerController::class)->except(['create', 'edit']);

            // Feedback moderation
            Route::get('feedback/counts', [FeedbackController::class, 'counts']);
            Route::post('feedback/{feedback}/approve', [FeedbackController::class, 'approve']);
            Route::post('feedback/{feedback}/unapprove', [FeedbackController::class, 'unapprove']);
            Route::apiResource('feedback', FeedbackController::class)->only(['index', 'destroy']);

            // Reports
            Route::get('reports/orders', [ReportController::class, 'orders']);
            Route::get('reports/orders/summary', [ReportController::class, 'ordersSummary']);
            Route::get('reports/orders/export', [ReportController::class, 'ordersExport']);
            Route::get('reports/payments', [ReportController::class, 'payments']);
            Route::get('reports/payments/summary', [ReportController::class, 'paymentsSummary']);
            Route::get('reports/payments/export', [ReportController::class, 'paymentsExport']);

            // Catalog management
            Route::apiResource('services', ServiceController::class)->except(['create', 'edit']);
            Route::apiResource('categories', CategoryController::class)->except(['create', 'edit']);
            Route::get('categories/{category}/services', [ProductController::class, 'servicesForCategory']);
            Route::post('products/{product}/toggle', [ProductController::class, 'toggle']);
            Route::apiResource('products', ProductController::class)->except(['create', 'edit']);
            Route::apiResource('price-lists', PriceListController::class)->except(['create', 'edit']);
            Route::post('price-lists/{priceList}/toggle', [PriceListController::class, 'toggle']);

            // Riders (staff management of rider accounts)
            Route::apiResource('riders', RiderController::class)->except(['create', 'edit']);

            // Branches — super-admin/admin only, mirrors the nested role:super-admin|admin group.
            Route::middleware('role:super-admin|admin')->group(function () {
                Route::post('branches/{branch}/toggle', [BranchController::class, 'toggle']);
                Route::apiResource('branches', BranchController::class)->except(['create', 'edit']);
            });
        });

        // ── Rider app (mirrors ["auth", "rider"] in web.php) ──
        Route::middleware('rider')->prefix('rider')->group(function () {
            Route::get('board', [RiderBoardController::class, 'board']);
            Route::post('orders/{orderId}/pickup', [RiderBoardController::class, 'markPickedUp']);
            Route::post('orders/{orderId}/start-delivery', [RiderBoardController::class, 'startDelivery']);
            Route::post('orders/{orderId}/deliver', [RiderBoardController::class, 'markDelivered']);
        });
    });
});
