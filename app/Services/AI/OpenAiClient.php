<?php

namespace App\Services\AI;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Minimal OpenAI Chat Completions client using Laravel's HTTP client.
 *
 * Replaces the openai-php/laravel package (which does not support
 * Laravel 13) with a zero-dependency implementation. Reads its
 * credentials from config/services.php => services.openai.*
 */
class OpenAiClient
{
    /**
     * Send a chat completion request and return the assistant message content.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $options  Extra payload options (response_format, max_tokens, ...)
     */
    public function chat(array $messages, array $options = []): string
    {
        $response = $this->request()
            ->post('/chat/completions', array_merge([
                'model' => config('services.openai.model', 'gpt-4o-mini'),
                'messages' => $messages,
            ], $options))
            ->throw()
            ->json();

        return (string) ($response['choices'][0]['message']['content'] ?? '');
    }

    /**
     * Same as chat() but expects (and decodes) a JSON object response.
     *
     * @return array<string, mixed>
     */
    public function chatJson(array $messages, array $options = []): array
    {
        $content = $this->chat($messages, array_merge([
            'response_format' => ['type' => 'json_object'],
        ], $options));

        return json_decode($content, true) ?: [];
    }

    protected function request(): PendingRequest
    {
        return Http::baseUrl('https://api.openai.com/v1')
            ->withToken((string) config('services.openai.key'))
            ->acceptJson()
            ->timeout(30)
            ->retry(2, 250, throw: false);
    }
}
