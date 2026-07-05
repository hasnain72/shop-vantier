<?php

namespace App\Services\Shipping;

use App\Models\Order;

interface ShippingProviderInterface
{
    /** Machine name of this provider (e.g. "smsa"). */
    public function getName(): string;

    /**
     * Create a shipment / AWB. Returns:
     * [
     *   'success'         => bool,
     *   'tracking_number' => string|null,   // AWB
     *   'tracking_url'    => string|null,
     *   'label_url'       => string|null,   // PDF link if provider returns one
     *   'raw_response'    => array,
     *   'message'         => string|null,
     * ]
     */
    public function createShipment(Order $order, array $context = []): array;

    /**
     * Poll live status by tracking number. Returns
     * ['success' => bool, 'status' => string|null, 'events' => array, 'raw_response' => array].
     */
    public function getTrackingStatus(string $trackingNumber): array;

    /** Public tracking URL for the customer. */
    public function trackingUrl(string $trackingNumber): string;

    /** Best-effort cancel by AWB. */
    public function cancelShipment(string $trackingNumber): array;
}
