<?php

namespace App\Http\Controllers\Api\Concerns;

use Carbon\Carbon;

/**
 * API equivalent of Livewire\Concerns\WithDateFilter.
 *
 * Livewire mapping:
 *   public string $period = 'all'      -> request('period', 'all')
 *   public ?string $dateFrom            -> request('date_from')
 *   public ?string $dateTo              -> request('date_to')
 *   dateRange() / applyDateFilter()     -> same methods below, reading from the request
 *     instead of component properties.
 *
 * Accepted values for `period`: all|today|week|month|year|custom
 * When period=custom, pass date_from / date_to as Y-m-d.
 */
trait AppliesDateFilter
{
    /**
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    protected function dateRange(): array
    {
        $period = request('period', 'all');

        return match ($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            'custom' => [
                request('date_from') ? Carbon::parse(request('date_from'))->startOfDay() : null,
                request('date_to') ? Carbon::parse(request('date_to'))->endOfDay() : null,
            ],
            default => [null, null],
        };
    }

    protected function applyDateFilter($query, string $column = 'created_at')
    {
        [$from, $to] = $this->dateRange();

        return $query
            ->when($from, fn ($q) => $q->where($column, '>=', $from))
            ->when($to, fn ($q) => $q->where($column, '<=', $to));
    }
}
