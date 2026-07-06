<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Api\Concerns\AppliesDateFilter;
use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

/**
 * Livewire\Admin\Feedback\Index -> FeedbackController (admin moderation queue)
 *   search/statusFilter/period... -> query params
 *   approve()/unapprove()/delete() -> approve()/unapprove()/destroy()
 *   pendingCount/approvedCount     -> counts()
 *
 * Public submission + approved-list display is handled separately by
 * PublicFeedbackController, matching Livewire\Public\FeedbackSection.
 */
class FeedbackController extends Controller
{
    use ApiResponse;
    use AppliesDateFilter;

    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // pending|approved|all

        $items = Feedback::with('approvedBy')
            ->when($status === 'pending', fn ($q) => $q->where('is_approved', false))
            ->when($status === 'approved', fn ($q) => $q->where('is_approved', true))
            ->when($request->query('search'), fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%' . $request->query('search') . '%')
                ->orWhere('message', 'like', '%' . $request->query('search') . '%')))
            ->tap(fn ($q) => $this->applyDateFilter($q))
            ->latest()
            ->paginate((int) $request->query('per_page', 12));

        return $this->ok($items, 'Feedback fetched successfully');
    }

    /** GET /admin/feedback/counts */
    public function counts()
    {
        return $this->ok([
            'pending' => Feedback::where('is_approved', false)->count(),
            'approved' => Feedback::where('is_approved', true)->count(),
        ], 'Feedback counts fetched successfully');
    }

    public function approve(Feedback $feedback)
    {
        $feedback->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return $this->ok($feedback, 'Feedback approved — now visible on the website.');
    }

    public function unapprove(Feedback $feedback)
    {
        $feedback->update([
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return $this->ok($feedback, 'Feedback hidden from the website.');
    }

    public function destroy(Feedback $feedback)
    {
        $feedback->delete();

        return $this->ok(null, 'Feedback deleted.');
    }
}
