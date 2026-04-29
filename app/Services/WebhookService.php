<?php

namespace App\Services;

use App\Jobs\SendWebhookJob;
use App\Models\Webhook;

class WebhookService
{
    public function dispatch(string $topic, array $payload): void
    {
        $webhooks = Webhook::where('topic', $topic)
            ->where('is_active', true)
            ->get();

        foreach ($webhooks as $webhook) {
            SendWebhookJob::dispatch($webhook, $payload);
        }
    }

    public function buildSignature(string $secret, string $body): string
    {
        return base64_encode(hash_hmac('sha256', $body, $secret, true));
    }
}
