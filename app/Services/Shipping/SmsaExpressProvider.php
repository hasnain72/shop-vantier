<?php

namespace App\Services\Shipping;

use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SMSA Express (Saudi courier) shipping provider.
 *
 * SMSA's XB / B2C API is a JSON REST endpoint that takes a "PassKey" bearer.
 * Actual endpoint paths differ by account type; keep them wired via config so
 * ops can adjust without redeploying.
 *
 * NOTE: without real credentials, calls will surface a clear "not configured"
 * error rather than hitting the network — controllers can then decide whether
 * to fall back to manual tracking-number input.
 */
class SmsaExpressProvider implements ShippingProviderInterface
{
    public function getName(): string
    {
        return 'smsa';
    }

    /**
     * Effective config: Admin → Settings → Shipping providers (store_settings)
     * overrides .env / config. Blank admin fields fall back to config.
     */
    private function settings(): array
    {
        $config = config('shipping.smsa');

        $o = StoreSetting::whereIn('key', [
            'shipping.smsa.env', 'shipping.smsa.passkey', 'shipping.smsa.account_no',
            'shipping.smsa.service_code', 'shipping.smsa.sender_name', 'shipping.smsa.sender_contact',
            'shipping.smsa.sender_phone', 'shipping.smsa.sender_email', 'shipping.smsa.sender_city',
        ])->pluck('value', 'key');

        if (!empty($o['shipping.smsa.env']))          $config['env']             = $o['shipping.smsa.env'];
        if (!empty($o['shipping.smsa.passkey']))      $config['passkey']         = $o['shipping.smsa.passkey'];
        if (!empty($o['shipping.smsa.account_no']))   $config['account_no']      = $o['shipping.smsa.account_no'];
        if (!empty($o['shipping.smsa.service_code'])) $config['default_service'] = $o['shipping.smsa.service_code'];

        foreach (['name' => 'sender_name', 'contact' => 'sender_contact', 'phone' => 'sender_phone',
                  'email' => 'sender_email', 'city' => 'sender_city'] as $senderKey => $settingSuffix) {
            $val = $o["shipping.smsa.{$settingSuffix}"] ?? null;
            if (!empty($val)) {
                $config['sender'][$senderKey] = $val;
            }
        }

        return $config;
    }

    public function createShipment(Order $order, array $context = []): array
    {
        $config = $this->settings();

        if (empty($config['passkey'])) {
            return $this->fail('SMSA API PassKey not configured.');
        }

        $shipping = (array) ($order->shipping_address ?? []);
        $sender   = array_merge($config['sender'] ?? [], $context['sender'] ?? []);
        $currency = $order->currency ?: $config['default_currency'];
        $service  = $context['service_code'] ?? $config['default_service'];

        // Payload shape matches SMSA's Shipment/CreateShipment schema.
        // If your account uses a different schema, adjust here — it's the only
        // place that touches the wire format.
        $payload = [
            'passkey'      => $config['passkey'],
            'accountNo'    => $config['account_no'],
            'refNo'        => (string) $order->id,
            'sentDate'     => now()->toDateString(),
            'idNo'         => null,
            'weight'       => $context['weight_kg'] ?? 1.0,
            'weightUnit'   => 'KG',
            'waybillType'  => 'PDF',
            'itemsCount'   => max(1, (int) ($context['pieces'] ?? 1)),
            'currency'     => $currency,
            'codAmount'    => $context['cod_amount'] ?? 0,
            'serviceCode'  => $service,
            'contentDesc'  => $context['content'] ?? 'Watch accessories',
            'shipper' => [
                'name'    => $sender['name'],
                'contact' => $sender['contact'],
                'phone'   => $sender['phone'],
                'email'   => $sender['email'],
                'city'    => $sender['city'],
                'country' => $sender['country'],
            ],
            'consignee' => [
                'name'    => trim(($shipping['first_name'] ?? '') . ' ' . ($shipping['last_name'] ?? '')) ?: 'Customer',
                'contact' => trim(($shipping['first_name'] ?? '') . ' ' . ($shipping['last_name'] ?? '')) ?: 'Customer',
                'phone'   => $order->phone ?? ($shipping['phone'] ?? null),
                'email'   => $order->email,
                'address' => trim(($shipping['address1'] ?? '') . ' ' . ($shipping['address2'] ?? '')),
                'city'    => $shipping['city']    ?? '',
                'country' => $shipping['country'] ?? 'SA',
                'zip'     => $shipping['zip']     ?? '',
            ],
        ];

        try {
            $response = Http::acceptJson()
                ->timeout(20)
                ->withHeaders(['Authorization' => 'PassKey ' . $config['passkey']])
                ->post(rtrim($config['base_url'], '/') . '/shipment/b2c/new', $payload);

            $body = $response->json() ?? [];

            if (!$response->ok() || empty($body['awbNo'] ?? null)) {
                Log::channel('api')->warning('SMSA createShipment failed', [
                    'order_id' => $order->id,
                    'status'   => $response->status(),
                    'body'     => $body,
                ]);
                return $this->fail($body['message'] ?? 'SMSA shipment creation failed.', $body);
            }

            $awb = (string) $body['awbNo'];

            return [
                'success'         => true,
                'tracking_number' => $awb,
                'tracking_url'    => $this->trackingUrl($awb),
                'label_url'       => $body['labelUrl'] ?? null,
                'raw_response'    => $body,
                'message'         => null,
            ];
        } catch (\Throwable $e) {
            Log::channel('api')->error('SMSA createShipment exception', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
            return $this->fail('SMSA unreachable: ' . $e->getMessage());
        }
    }

    public function getTrackingStatus(string $trackingNumber): array
    {
        $config = $this->settings();

        if (empty($config['passkey'])) {
            return ['success' => false, 'status' => null, 'events' => [], 'raw_response' => [], 'message' => 'PassKey not configured.'];
        }

        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->withHeaders(['Authorization' => 'PassKey ' . $config['passkey']])
                ->get(rtrim($config['base_url'], '/') . '/tracking/' . urlencode($trackingNumber));

            $body = $response->json() ?? [];

            return [
                'success'      => $response->ok(),
                'status'       => $body['status']  ?? null,
                'events'       => $body['events']  ?? [],
                'raw_response' => $body,
                'message'      => $body['message'] ?? null,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'status' => null, 'events' => [], 'raw_response' => [], 'message' => $e->getMessage()];
        }
    }

    public function trackingUrl(string $trackingNumber): string
    {
        return 'https://track.smsaexpress.com/en/tracking?trackingNumber=' . urlencode($trackingNumber);
    }

    public function cancelShipment(string $trackingNumber): array
    {
        $config = $this->settings();
        if (empty($config['passkey'])) {
            return ['success' => false, 'message' => 'PassKey not configured.'];
        }

        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->withHeaders(['Authorization' => 'PassKey ' . $config['passkey']])
                ->post(rtrim($config['base_url'], '/') . '/shipment/cancel', [
                    'awbNo' => $trackingNumber,
                ]);

            return [
                'success'      => $response->ok() && ($response->json('success') ?? false),
                'raw_response' => $response->json() ?? [],
                'message'      => $response->json('message'),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function fail(string $message, array $raw = []): array
    {
        return [
            'success'         => false,
            'tracking_number' => null,
            'tracking_url'    => null,
            'label_url'       => null,
            'raw_response'    => $raw,
            'message'         => $message,
        ];
    }
}
