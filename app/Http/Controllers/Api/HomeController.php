<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Service;

/**
 * Livewire\Public\HomePage -> HomeController.
 * (Livewire\Public\Home duplicated ContactSection + FeedbackSection inline;
 * the mobile app should instead call ContactController::store(),
 * PublicFeedbackController::index()/store(), and this endpoint for services —
 * three focused endpoints instead of one page-shaped one.)
 */
class HomeController extends Controller
{
    use ApiResponse;

    /** GET /public/home — featured services for the landing screen. */
    public function index()
    {
        $services = Service::active()->take(6)->get();

        return $this->ok(compact('services'), 'Home data fetched successfully');
    }
}
