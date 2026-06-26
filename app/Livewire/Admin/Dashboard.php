<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithDateFilter;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Services\AI\AiInsightsService;
use Livewire\Component;

class Dashboard extends Component
{
    use WithDateFilter;

    public function mount(): void
    {
        // Dashboards read most naturally scoped to "today" by default.
        $this->period = 'today';
    }

    public function getListeners(): array
    {
        $branchId = auth()->user()->branch_id ?? 0;
        return ["echo-private:branches.{$branchId},.order.status.updated" => "\$refresh"];
    }

    public function render(AiInsightsService $ai)
    {
        $branchId = auth()->user()->branch_id;
        $scope = fn ($q) => $branchId ? $q->where("branch_id", $branchId) : $q;
        [$from, $to] = $this->dateRange();

        // Apply the active period window to a query (no-op for "all time").
        $inPeriod = fn ($q) => $q
            ->when($from, fn ($w) => $w->where("created_at", ">=", $from))
            ->when($to, fn ($w) => $w->where("created_at", "<=", $to));

        $stats = [
            // Orders created in the selected period.
            "orders_today"  => $inPeriod($scope(Order::query()))->count(),
            // Of those, the ones not yet delivered…
            "pending"       => $inPeriod($scope(Order::query()))->whereNotIn("status", ["delivered"])->count(),
            // …and the ones delivered.
            "delivered"     => $inPeriod($scope(Order::query()))->where("status", "delivered")->count(),
            // Money collected in the period.
            "revenue_today" => (float) Payment::query()
                ->when($from, fn ($w) => $w->where("created_at", ">=", $from))
                ->when($to, fn ($w) => $w->where("created_at", "<=", $to))
                ->when($branchId, fn ($q) => $q->whereHas("order", fn ($o) => $o->where("branch_id", $branchId)))
                ->sum("amount"),
            // New customers registered in the period.
            "customers"     => $inPeriod($scope(Customer::query()))->count(),
            // Distinct riders who handled orders in the period.
            "riders_online" => $inPeriod($scope(Order::query()))
                ->whereNotNull("rider_id")->distinct()->count("rider_id"),
        ];

        $raw = $scope(Order::query())
            ->where("created_at", ">=", now()->subDays(30))
            ->selectRaw("DATE(created_at) d, SUM(total) v")
            ->groupBy("d")->orderBy("d")->pluck("v", "d");

        $revenueSeries = ["labels" => [], "values" => []];
        foreach (range(29, 0) as $daysAgo) {
            $date = now()->subDays($daysAgo);
            $revenueSeries["labels"][] = $date->format("d M");
            $revenueSeries["values"][] = (float) ($raw[$date->toDateString()] ?? 0);
        }

        $forecast = rescue(fn () => $ai->revenueForecast(auth()->user()->branch), [], false);

        return view("livewire.admin.dashboard", compact("stats", "revenueSeries", "forecast"))
            ->layout("layouts.admin", ["title" => "Dashboard"]);
    }
}