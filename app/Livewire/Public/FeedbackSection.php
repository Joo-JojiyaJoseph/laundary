<?php

namespace App\Livewire\Public;

use App\Models\Feedback;
use App\Models\OtpCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;

class FeedbackSection extends Component
{
    // ── Review fields ────────────────────────────────────────────────
    #[Validate('required|string|min:2|max:120')]
    public string $name = '';

    #[Validate('required|regex:/^[0-9+\-\s]{8,15}$/')]
    public string $mobile = '';

    #[Validate('required|integer|min:1|max:5')]
    public int $rating = 5;

    #[Validate('required|string|min:5|max:1000')]
    public string $message = '';

    // ── OTP flow ─────────────────────────────────────────────────────
    public bool $awaitingCode = false;   // true once a code has been sent
    public string $code = '';            // the 6 digits the user types

    /** Normalise a mobile number to digits only, for matching. */
    protected function normalizedMobile(): string
    {
        return preg_replace('/\D/', '', $this->mobile);
    }

    public function validationAttributes(): array
    {
        return ['mobile' => 'mobile number', 'message' => 'feedback'];
    }

    /** Step 1 — validate the review, then generate & "send" an OTP. */
    public function sendCode(): void
    {
        $this->validate();

        $mobile = $this->normalizedMobile();

        // Throttle: max 5 codes per number per hour.
        $recent = OtpCode::where('mobile', $mobile)
            ->where('created_at', '>=', now()->subHour())
            ->count();
        if ($recent >= 5) {
            $this->addError('mobile', 'Too many codes requested. Please try again later.');
            return;
        }

        $code = (string) random_int(100000, 999999);

        OtpCode::create([
            'mobile' => $mobile,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Delivery: in production wire this to SMS / WhatsApp Cloud API.
        // Here we always log it, and in debug mode surface it so the flow
        // is usable without an SMS gateway configured.
        Log::info("Feedback OTP for {$mobile}: {$code}");

        $this->awaitingCode = true;
        $this->code = '';

        $hint = config('app.debug') ? " (dev code: {$code})" : '';
        $this->dispatch('notify', type: 'success', title: 'Code sent',
            message: "We sent a 6-digit code to your mobile.{$hint}");
    }

    /** Step 2 — verify the OTP and store the verified feedback. */
    public function verifyAndSubmit(): void
    {
        $this->validate(['code' => 'required|digits:6']);

        $mobile = $this->normalizedMobile();

        $otp = OtpCode::where('mobile', $mobile)
            ->whereNull('consumed_at')
            ->where('expires_at', '>=', now())
            ->latest()
            ->first();

        if (! $otp || ! hash_equals($otp->code, trim($this->code))) {
            $this->addError('code', 'That code is incorrect or has expired. Please try again.');
            return;
        }

        try {
            $otp->update(['consumed_at' => now()]);

            Feedback::create([
                'name' => $this->name,
                'mobile' => $mobile,
                'rating' => $this->rating,
                'message' => $this->message,
                'is_verified' => true,
                'verified_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
            $this->addError('code', 'We could not save your feedback. Please try again.');
            return;
        }

        $this->reset(['name', 'mobile', 'rating', 'message', 'code', 'awaitingCode']);
        $this->rating = 5;

        $this->dispatch('notify', type: 'success', title: 'Thank you!',
            message: 'Your verified feedback is now live on our site.');
        $this->dispatch('feedback-added');
    }

    /** Let the user go back and edit their details / number. */
    public function editDetails(): void
    {
        $this->awaitingCode = false;
        $this->code = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.public.feedback-section', [
            'reviews' => Feedback::public()->latest()->take(6)->get(),
            'averageRating' => round((float) Feedback::public()->avg('rating'), 1),
            'reviewCount' => Feedback::public()->count(),
        ]);
    }
}
