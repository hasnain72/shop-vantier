<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    public function charge(array $data): array;
    public function refund(string $transactionId, float $amount): array;
    public function void(string $transactionId): array;
    public function getGatewayName(): string;
}
