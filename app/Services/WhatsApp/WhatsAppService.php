<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin wrapper around the WhatsApp Cloud API (Graph API v21).
 * Approved message templates must exist in Meta Business Manager.
 */
class WhatsAppService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = "https://graph.facebook.com/v21.0/" . config("services.whatsapp.phone_number_id");
    }

    public function sendTemplate(string $to, string $template, array $parameters = [], string $lang = "en"): bool
    {
        return $this->post([
            "messaging_product" => "whatsapp",
            "to" => $this->normalize($to),
            "type" => "template",
            "template" => [
                "name" => $template,
                "language" => ["code" => $lang],
                "components" => [[
                    "type" => "body",
                    "parameters" => array_map(fn ($p) => ["type" => "text", "text" => (string) $p], $parameters),
                ]],
            ],
        ]);
    }

    public function sendText(string $to, string $message): bool
    {
        return $this->post([
            "messaging_product" => "whatsapp",
            "to" => $this->normalize($to),
            "type" => "text",
            "text" => ["preview_url" => true, "body" => $message],
        ]);
    }

    public function sendDocument(string $to, string $url, string $filename, ?string $caption = null): bool
    {
        return $this->post([
            "messaging_product" => "whatsapp",
            "to" => $this->normalize($to),
            "type" => "document",
            "document" => array_filter(["link" => $url, "filename" => $filename, "caption" => $caption]),
        ]);
    }

    protected function post(array $payload): bool
    {
        $response = Http::withToken(config("services.whatsapp.token"))
            ->post("{$this->baseUrl}/messages", $payload);

        if ($response->failed()) {
            Log::warning("WhatsApp send failed", ["body" => $response->json()]);
        }

        return $response->successful();
    }

    protected function normalize(string $mobile): string
    {
        $digits = preg_replace("/\D/", "", $mobile);
        return strlen($digits) === 10 ? "91{$digits}" : $digits; // default to India CC
    }
}
