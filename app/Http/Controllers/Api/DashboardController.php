<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Api\Concerns\AppliesDateFilter;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Services\AI\AiInsightsService;
use Illuminate\Http\Request;

/**
 * Livewire\Admin\Dashboard -> DashboardController
 *   mount() default period='today'  -> defaults to "today" below if no ?period= given
 *   getListeners() ($refresh on order.status.updated) -> not applicable over REST;
 *     the mobile app should just re-call GET /dashboard, or subscribe to the
 *     same Echo/Pusher channel directly for push updates.
 *   render()                        -> index()
 */
class DashboardController extends Controller
{
    use ApiResponse;
    use AppliesDateFilter;

    public function index(Request $request, AiInsightsService $ai)
    {
        // Dashboards read most naturally scoped to "today" by default.
        if (! $request->query('period')) {
            $request->query->set('period', 'today');
        }

        $user = $request->user();
        $branchId = $user->branch_id;
        $scope = fn ($q) => $branchId ? $q->where('branch_id', $branchId) : $q;
        [$from, $to] = $this->dateRange();

        $inPeriod = fn ($q) => $q
            ->when($from, fn ($w) => $w->where('created_at', '>=', $from))
            ->when($to, fn ($w) => $w->where('created_at', '<=', $to));

        $stats = [
            'orders_today' => $inPeriod($scope(Order::query()))->count(),
            'pending' => $inPeriod($scope(Order::query()))->whereNotIn('status', ['delivered'])->count(),
            'delivered' => $inPeriod($scope(Order::query()))->where('status', 'delivered')->count(),
            'revenue_today' => (float) Payment::query()
                ->when($from, fn ($w) => $w->where('created_at', '>=', $from))
                ->when($to, fn ($w) => $w->where('created_at', '<=', $to))
                ->when($branchId, fn ($q) => $q->whereHas('order', fn ($o) => $o->where('branch_id', $branchId)))
                ->sum('amount'),
            'customers' => $inPeriod($scope(Customer::query()))->count(),
            'riders_online' => $inPeriod($scope(Order::query()))
                ->whereNotNull('rider_id')->distinct()->count('rider_id'),
        ];

        $raw = $scope(Order::query())
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) d, SUM(total) v')
            ->groupBy('d')->orderBy('d')->pluck('v', 'd');

        $revenueSeries = ['labels' => [], 'values' => []];
        foreach (range(29, 0) as $daysAgo) {
            $date = now()->subDays($daysAgo);
            $revenueSeries['labels'][] = $date->format('d M');
            $revenueSeries['values'][] = (float) ($raw[$date->toDateString()] ?? 0);
        }

        $forecast = rescue(fn () => $ai->revenueForecast($user->branch), [], false);

        return $this->ok(compact('stats', 'revenueSeries', 'forecast'), 'Dashboard data fetched successfully');
    }
}
