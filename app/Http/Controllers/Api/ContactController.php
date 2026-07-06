<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreContactMessageRequest;
use App\Models\ContactMessage;

/**
 * Livewire\Public\ContactSection::submit() (also duplicated in
 * Livewire\Public\Home::submit() — both do the exact same thing) -> ContactController.
 * Public, unauthenticated.
 */
class ContactController extends Controller
{
    use ApiResponse;

    public function store(StoreContactMessageRequest $request)
    {
        try {
            $message = ContactMessage::create($request->validated());
        } catch (\Throwable $e) {
            report($e);

            return $this->fail('Could not send your message. Please try again or message us on WhatsApp.', 500);
        }

        return $this->created($message, 'Thanks for reaching out — we will reply within a few hours.');
    }
}
