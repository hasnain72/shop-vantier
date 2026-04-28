<?php

namespace App\Services\Payment;

use Exception;

class StripeGateway implements PaymentGatewayInterface
{
    public function getGatewayName(): string { return 'stripe'; }

    public function charge(array $data): array
    {
        // Requires stripe/stripe-php package
        // \Stripe\Stripe::setApiKey(config('payment.stripe.secret'));
        // $intent = \Stripe\PaymentIntent::create([...]);
        return [
            'success'        => true,
            'transaction_id' => 'pi_test_' . uniqid(),
            'message'        => 'Payment captured via Stripe.',
            'gateway_response' => $data,
        ];
    }

    public function refund(string $transactionId, float $amount): array
    {
        return [
            'success'        => true,
            'transaction_id' => 're_test_' . uniqid(),
            'message'        => "Refund of \${$amount} issued via Stripe.",
        ];
    }

    public function void(string $transactionId): array
    {
        return ['success' => true, 'message' => 'PaymentIntent cancelled.'];
    }
}
