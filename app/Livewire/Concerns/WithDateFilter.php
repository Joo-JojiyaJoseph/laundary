<?php

namespace App\Livewire\Concerns;

use Carbon\Carbon;

/**
 * Adds a reusable "today / week / month / year / custom" date filter to any
 * Livewire list component. Use applyDateFilter() inside the query builder.
 */
trait WithDateFilter
{
    /** all|today|week|month|year|custom */
    public string $period = 'all';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public function updatedPeriod(): void
    {
        if ($this->period !== 'custom') {
            $this->dateFrom = null;
            $this->dateTo = null;
        }
        $this->resetPageIfPaginated();
    }

    public function updatedDateFrom(): void
    {
        $this->period = 'custom';
        $this->resetPageIfPaginated();
    }

    public function updatedDateTo(): void
    {
        $this->period = 'custom';
        $this->resetPageIfPaginated();
    }

    protected function resetPageIfPaginated(): void
    {
        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    /**
     * Resolve the active period into a [from, to] window of Carbon instances.
     * Returns [null, null] when no date constraint should be applied.
     *
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    public function dateRange(): array
    {
        return match ($this->period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            'custom' => [
                $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay() : null,
                $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay() : null,
            ],
            default => [null, null],
        };
    }

    /**
     * Apply the active date filter to an Eloquent/Query builder.
     */
    public function applyDateFilter($query, string $column = 'created_at')
    {
        [$from, $to] = $this->dateRange();

        return $query
            ->when($from, fn ($q) => $q->where($column, '>=', $from))
            ->when($to, fn ($q) => $q->where($column, '<=', $to));
    }

    /** Human label for the active period (used in headings / exports). */
    public function periodLabel(): string
    {
        [$from, $to] = $this->dateRange();

        return match ($this->period) {
            'today' => 'Today',
            'week' => 'This week',
            'month' => 'This month',
            'year' => 'This year',
            'custom' => trim(
                ($from ? $from->format('d M Y') : '…') . ' – ' . ($to ? $to->format('d M Y') : '…')
            ),
            default => 'All time',
        };
    }
}
