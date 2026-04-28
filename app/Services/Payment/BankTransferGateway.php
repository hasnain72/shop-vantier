<?php

namespace App\Services\Payment;

class BankTransferGateway implements PaymentGatewayInterface
{
    public function getGatewayName(): string { return 'bank_transfer'; }

    public function charge(array $data): array
    {
        $details = config('payment.bank_transfer.account_details', 'Contact store for bank details.');

        return [
            'success'        => false, // pending manual confirmation
            'transaction_id' => 'BT-' . uniqid(),
            'message'        => 'Bank transfer instructions: ' . $details,
            'pending'        => true,
        ];
    }

    public function refund(string $transactionId, float $amount): array
    {
        return ['success' => true, 'message' => 'Bank transfer refund initiated manually.'];
    }

    public function void(string $transactionId): array
    {
        return ['success' => true, 'message' => 'Bank transfer voided.'];
    }
}
