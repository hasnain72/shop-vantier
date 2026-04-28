<?php

namespace App\Services\Payment;

class PayPalGateway implements PaymentGatewayInterface
{
    public function getGatewayName(): string { return 'paypal'; }

    public function charge(array $data): array
    {
        // Integrate PayPal Orders API (paypalintegrations/paypal-rest-api-client or direct HTTP)
        return [
            'success'        => true,
            'transaction_id' => 'PAYID-' . strtoupper(uniqid()),
            'message'        => 'Payment captured via PayPal.',
        ];
    }

    public function refund(string $transactionId, float $amount): array
    {
        return ['success' => true, 'transaction_id' => 'REFUND-' . uniqid(), 'message' => 'PayPal refund issued.'];
    }

    public function void(string $transactionId): array
    {
        return ['success' => true, 'message' => 'PayPal authorization voided.'];
    }
}
