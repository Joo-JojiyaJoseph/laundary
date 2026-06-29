<?php

namespace App\Livewire\Public;

use App\Models\ContactMessage;
use App\Models\Feedback;
use Livewire\Attributes\Validate;
use Livewire\Component;


class Home extends Component
{

    public string $name = "";

    public string $email = "";

    public string $phone = "";

    public string $message = "";


    public string $ratingName = '';

    public int $rating = 5;

    public string $ratingMessage = '';

    public bool $submitted = false;

    // Rating
    public function ratingSubmit(): void
    {
        $this->validate([
            'ratingName' => 'required|min:2|max:80',
            'rating' => 'required|integer|min:1|max:5',
            'ratingMessage' => 'required|min:10|max:1000',
        ]);

        try {
            Feedback::create([
                'name' => $this->ratingName,
                'rating' => $this->rating,
                'message' => $this->ratingMessage,
                'is_approved' => false, // waits for admin approval
            ]);

            $this->reset(['name', 'message']);
            $this->rating = 5;
            $this->submitted = true;

            $this->dispatch(
                'notify',
                type: 'success',
                title: 'Thank you!',
                message: 'Your feedback was submitted and will appear once our team approves it.'
            );
            $this->reset();
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('notify', type: 'error', title: 'Could not submit', message: 'Please try again in a moment.');
        }
    }


    // Email sending
    public function submit(): void
    {
        $this->validate([
            'name' => 'required|min:2|max:80',
            'email' => 'required|email',
            'phone' => 'required|min:8|max:15',
            'message' => 'required|min:10|max:2000',
        ]);

        try {
            ContactMessage::create($this->only(["name", "email", "phone", "message"]));
            $this->reset(["name", "email", "phone", "message"]);
            $this->dispatch("notify", type: "success", title: "Message sent!", message: "Thanks for reaching out — we will reply within a few hours.");
            $this->reset();
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch("notify", type: "error", title: "Could not send", message: "Please try again or message us on WhatsApp.");
        }
    }

    public function render()
    {
        return view("livewire.public.home", [
            'reviews' => Feedback::approved()->latest()->take(9)->get(),
            'averageRating' => round((float) Feedback::approved()->avg('rating'), 1),
            'reviewCount' => Feedback::approved()->count(),
        ])->layout("layouts.public", ["title" => "Laundrix — Laundry, handled."]);
    }
}
