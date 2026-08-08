<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Thin wrapper over the Anthropic Messages API. Kept intentionally small: it
 * sends a system + user prompt and returns the model's text, plus a helper that
 * expects the reply to be JSON and decodes it. No SDK, just the HTTP client.
 */
class ClaudeClient
{
    private const ENDPOINT = 'https://api.anthropic.com/v1/messages';

    /**
     * Send one prompt and return the model's plain-text reply.
     */
    public function text(string $system, string $prompt, int $maxTokens = 2048): string
    {
        $key = config('services.anthropic.key');

        if (empty($key)) {
            throw new RuntimeException('ANTHROPIC_API_KEY is not configured.');
        }

        $response = Http::withHeaders([
            'x-api-key' => $key,
            'anthropic-version' => config('services.anthropic.version'),
            'content-type' => 'application/json',
        ])
            ->timeout(120)
            ->post(self::ENDPOINT, [
                'model' => config('services.anthropic.model'),
                'max_tokens' => $maxTokens,
                'system' => $system,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Anthropic API error: '.$response->status().' '.$response->body());
        }

        return (string) $response->json('content.0.text', '');
    }

    /**
     * Send a prompt whose reply is expected to be a JSON value and decode it.
     * Tolerates the model wrapping the JSON in a ```json fenced block.
     *
     * @return mixed
     */
    public function json(string $system, string $prompt, int $maxTokens = 4096)
    {
        $raw = trim($this->text($system.' Respond with valid JSON only, no prose, no code fences.', $prompt, $maxTokens));

        $raw = preg_replace('/^```(?:json)?\s*|\s*```$/', '', $raw) ?? $raw;

        $decoded = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Anthropic reply was not valid JSON: '.substr($raw, 0, 300));
        }

        return $decoded;
    }
}
