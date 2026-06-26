<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithDateFilter;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Rider;
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
            "orders_today"   => $inPeriod($scope(Order::query()))->count(),
            "pending"        => $scope(Order::whereNotIn("status", ["delivered"]))->count(),
            "delivered"      => $scope(Order::where("status", "delivered"))
                ->when($from, fn ($w) => $w->where("delivered_at", ">=", $from))
                ->when($to, fn ($w) => $w->where("delivered_at", "<=", $to))
                ->count(),
            "revenue_today"  => (float) Payment::query()
                ->when($from, fn ($w) => $w->where("created_at", ">=", $from))
                ->when($to, fn ($w) => $w->where("created_at", "<=", $to))
                ->when($branchId, fn ($q) => $q->whereHas("order", fn ($o) => $o->where("branch_id", $branchId)))
                ->sum("amount"),
            "customers"      => $scope(Customer::query())->count(),
            "riders_online"  => $scope(Rider::where("is_online", true))->count(),
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
