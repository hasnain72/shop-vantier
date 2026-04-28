<?php

namespace App\Services\Payment;

class CodGateway implements PaymentGatewayInterface
{
    public function getGatewayName(): string { return 'cod'; }

    public function charge(array $data): array
    {
        return [
            'success'        => true,
            'transaction_id' => 'COD-' . uniqid(),
            'message'        => 'Cash on delivery — payment pending on delivery.',
        ];
    }

    public function refund(string $transactionId, float $amount): array
    {
        return ['success' => true, 'message' => 'COD refund marked.'];
    }

    public function void(string $transactionId): array
    {
        return ['success' => true, 'message' => 'COD order voided.'];
    }
}
