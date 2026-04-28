<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrackingService
{
    private array $trackingUrls = [
        'dhl'          => 'https://www.dhl.com/en/express/tracking.html?AWB={number}',
        'fedex'        => 'https://www.fedex.com/fedextrack/?trknbr={number}',
        'ups'          => 'https://www.ups.com/track?tracknum={number}',
        'pakistan_post'=> 'https://www.pakpost.gov.pk/track/?trackingid={number}',
        'tcs'          => 'https://www.tcsexpress.com/track/{number}',
        'leopards'     => 'https://leopardscourier.com/track-your-parcel/?track_numbers={number}',
    ];

    public function getTrackingUrl(string $carrier, string $trackingNumber): string
    {
        $template = $this->trackingUrls[strtolower($carrier)] ?? null;
        if (!$template) return '#';
        return str_replace('{number}', urlencode($trackingNumber), $template);
    }

    public function getTrackingInfo(string $carrier, string $trackingNumber): array
    {
        $carrier = strtolower($carrier);

        try {
            return match ($carrier) {
                'dhl'          => $this->trackDhl($trackingNumber),
                'fedex'        => $this->trackFedEx($trackingNumber),
                'ups'          => $this->trackUps($trackingNumber),
                'leopards'     => $this->trackLeopards($trackingNumber),
                default        => $this->fallbackTracking($carrier, $trackingNumber),
            };
        } catch (\Throwable $e) {
            Log::warning("Tracking lookup failed for {$carrier}/{$trackingNumber}: " . $e->getMessage());
            return $this->unknownStatus($trackingNumber);
        }
    }

    private function trackDhl(string $number): array
    {
        // Requires DHL Tracking API credentials in config
        $apiKey = config('services.dhl.api_key');
        if (!$apiKey) return $this->unknownStatus($number);

        $response = Http::withHeaders(['DHL-API-Key' => $apiKey])
            ->get("https://api-eu.dhl.com/track/shipments", ['trackingNumber' => $number]);

        if (!$response->successful()) return $this->unknownStatus($number);

        $shipment = $response->json('shipments.0', []);
        $events   = collect($shipment['events'] ?? [])->map(fn ($e) => [
            'timestamp'   => $e['timestamp'] ?? null,
            'location'    => $e['location']['address']['addressLocality'] ?? null,
            'description' => $e['description'] ?? null,
        ])->all();

        return [
            'status'      => $shipment['status']['statusCode'] ?? 'unknown',
            'last_update' => $events[0]['timestamp'] ?? null,
            'location'    => $events[0]['location'] ?? null,
            'events'      => $events,
        ];
    }

    private function trackFedEx(string $number): array
    {
        // Stub — requires FedEx OAuth token flow
        return $this->unknownStatus($number);
    }

    private function trackUps(string $number): array
    {
        // Stub — requires UPS API credentials
        return $this->unknownStatus($number);
    }

    private function trackLeopards(string $number): array
    {
        $apiKey    = config('services.leopards.api_key');
        $apiPassword = config('services.leopards.api_password');
        if (!$apiKey) return $this->unknownStatus($number);

        $response = Http::get('https://merchantapi.leopardscourier.com/api/trackBookedPacket/', [
            'api_key'      => $apiKey,
            'api_password' => $apiPassword,
            'track_numbers'=> $number,
        ]);

        if (!$response->successful()) return $this->unknownStatus($number);

        $data   = $response->json('packet_list.0', []);
        $events = collect($data['Activity'] ?? [])->map(fn ($e) => [
            'timestamp'   => $e['DateTime'] ?? null,
            'location'    => $e['Origin'] ?? null,
            'description' => $e['Status'] ?? null,
        ])->all();

        return [
            'status'      => $data['Status'] ?? 'unknown',
            'last_update' => $events[0]['timestamp'] ?? null,
            'location'    => $events[0]['location'] ?? null,
            'events'      => $events,
        ];
    }

    private function fallbackTracking(string $carrier, string $number): array
    {
        return $this->unknownStatus($number);
    }

    private function unknownStatus(string $number): array
    {
        return [
            'status'      => 'unknown',
            'last_update' => null,
            'location'    => null,
            'events'      => [],
        ];
    }
}
