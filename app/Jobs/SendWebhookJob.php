<?php

namespace App\Jobs;

use App\Models\Webhook;
use App\Services\WebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries    = 3;
    public int $backoff  = 60; // seconds between retries

    public function __construct(
        private readonly Webhook $webhook,
        private readonly array $payload
    ) {}

    public function handle(WebhookService $webhookService): void
    {
        $body      = json_encode($this->payload);
        $signature = $webhookService->buildSignature($this->webhook->secret, $body);

        $response = Http::withHeaders([
            'Content-Type'              => 'application/json',
            'X-Webhook-Topic'           => $this->webhook->topic,
            'X-Webhook-Hmac-Sha256'     => $signature,
        ])
        ->timeout(10)
        ->post($this->webhook->address, $this->payload);

        $this->webhook->update([
            'last_triggered_at'  => now(),
            'last_status_code'   => $response->status(),
        ]);

        if (!$response->successful()) {
            $this->handleFailure($response->status());
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Webhook delivery failed permanently', [
            'webhook_id' => $this->webhook->id,
            'topic'      => $this->webhook->topic,
            'address'    => $this->webhook->address,
            'error'      => $exception->getMessage(),
        ]);

        // Disable the webhook after 5 consecutive failures
        $this->webhook->increment('failure_count');

        if ($this->webhook->failure_count >= 5) {
            $this->webhook->update(['is_active' => false]);
        }
    }

    private function handleFailure(int $statusCode): void
    {
        throw new \RuntimeException("Webhook delivery failed with status {$statusCode}");
    }
}
