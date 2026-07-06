<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreFeedbackRequest;
use App\Models\Feedback;

/**
 * Livewire\Public\FeedbackSection (also duplicated as the "rating" half of
 * Livewire\Public\Home) -> PublicFeedbackController. Public, unauthenticated.
 *
 *   render()'s "reviews"/"averageRating"/"reviewCount" -> index()
 *   submit()                                            -> store() (always saved
 *                                                          with is_approved=false,
 *                                                          pending admin moderation
 *                                                          via FeedbackController)
 */
class PublicFeedbackController extends Controller
{
    use ApiResponse;

    /** GET /public/feedback — approved reviews shown on the website. */
    public function index()
    {
        return $this->ok([
            'reviews' => Feedback::approved()->latest()->take(9)->get(),
            'average_rating' => round((float) Feedback::approved()->avg('rating'), 1),
            'review_count' => Feedback::approved()->count(),
        ], 'Reviews fetched successfully');
    }

    public function store(StoreFeedbackRequest $request)
    {
        try {
            $feedback = Feedback::create([
                ...$request->validated(),
                'is_approved' => false, // waits for admin approval
            ]);
        } catch (\Throwable $e) {
            report($e);

            return $this->fail('Could not submit your feedback. Please try again in a moment.', 500);
        }

        return $this->created($feedback, 'Your feedback was submitted and will appear once our team approves it.');
    }
}
