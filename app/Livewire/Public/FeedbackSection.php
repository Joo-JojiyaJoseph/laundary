<?php

namespace App\Livewire\Public;

use App\Models\Feedback;
use Livewire\Attributes\Validate;
use Livewire\Component;

class FeedbackSection extends Component
{
    #[Validate('required|min:2|max:80')]
    public string $name = '';

    #[Validate('required|integer|min:1|max:5')]
    public int $rating = 5;

    #[Validate('required|min:10|max:1000')]
    public string $message = '';

    public bool $submitted = false;

    public function submit(): void
    {
        $this->validate();

        try {
            Feedback::create([
                'name' => $this->name,
                'rating' => $this->rating,
                'message' => $this->message,
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

    public function render()
    {
        return view('livewire.public.feedback-section', [
            'reviews' => Feedback::approved()->latest()->take(9)->get(),
            'averageRating' => round((float) Feedback::approved()->avg('rating'), 1),
            'reviewCount' => Feedback::approved()->count(),
        ]);
    }
}
