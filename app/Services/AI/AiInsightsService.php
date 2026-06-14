<?php

namespace App\Services\AI;

use App\Models\Branch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Business intelligence: revenue forecasts, pricing suggestions,
 * customer insights. Results are cached to control token spend.
 */
class AiInsightsService
{
    public function __construct(protected OpenAiClient $ai)
    {
    }

    public function revenueForecast(?Branch $branch = null): array
    {
        $key = "ai.forecast." . ($branch?->id ?? "all");

        return Cache::remember($key, now()->addHours(12), function () use ($branch) {
            $history = DB::table("orders")
                ->when($branch, fn ($q) => $q->where("branch_id", $branch->id))
                ->whereNull("deleted_at")
                ->where("created_at", ">=", now()->subMonths(6))
                ->selectRaw("DATE_FORMAT(created_at, \"%Y-%m\") as month, SUM(total) as revenue, COUNT(*) as orders")
                ->groupBy("month")->orderBy("month")->get();

            return $this->ai->chatJson([
                ["role" => "system", "content" =>
                    "You are a forecasting engine for a laundry SaaS. Given monthly revenue history as JSON, " .
                    "return STRICT JSON: {\"next_month_revenue\": number, \"growth_percent\": number, " .
                    "\"trend\": \"up|down|flat\", \"insights\": [string, string, string]}"],
                ["role" => "user", "content" => $history->toJson()],
            ]);
        });
    }

    public function pricingSuggestion(string $productName, float $currentPrice, array $marketContext = []): array
    {
        return $this->ai->chatJson([
            ["role" => "system", "content" =>
                "Suggest optimal laundry pricing. Return STRICT JSON: " .
                "{\"suggested_price\": number, \"min\": number, \"max\": number, \"rationale\": string}"],
            ["role" => "user", "content" => json_encode([
                "product" => $productName, "current_price" => $currentPrice, "context" => $marketContext,
            ])],
        ]);
    }

    public function draftWhatsAppMessage(string $purpose, array $context = []): string
    {
        return trim($this->ai->chat([
            ["role" => "system", "content" =>
                "Write a short, warm, professional WhatsApp message for a laundry business. " .
                "No emojis overload (max 1), under 60 words, plain text."],
            ["role" => "user", "content" => "Purpose: {$purpose}. Context: " . json_encode($context)],
        ]));
    }
}
