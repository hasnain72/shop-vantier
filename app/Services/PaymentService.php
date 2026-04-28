<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Transaction;
use App\Services\Payment\BankTransferGateway;
use App\Services\Payment\CodGateway;
use App\Services\Payment\PaymentGatewayInterface;
use App\Services\Payment\PayPalGateway;
use App\Services\Payment\StripeGateway;
use InvalidArgumentException;

class PaymentService
{
    public function getGateway(string $name): PaymentGatewayInterface
    {
        return match ($name) {
            'stripe'        => new StripeGateway(),
            'paypal'        => new PayPalGateway(),
            'cod'           => new CodGateway(),
            'bank_transfer' => new BankTransferGateway(),
            default         => throw new InvalidArgumentException("Unknown gateway: {$name}"),
        };
    }

    public function processPayment(Order $order, string $gateway, array $paymentData = []): Transaction
    {
        $gw     = $this->getGateway($gateway);
        $result = $gw->charge(array_merge($paymentData, [
            'amount'   => $order->total_price,
            'currency' => $order->currency,
            'order_id' => $order->id,
        ]));

        $status = ($result['success'] ?? false) ? 'success' : (($result['pending'] ?? false) ? 'pending' : 'failure');

        $transaction = Transaction::create([
            'order_id'         => $order->id,
            'kind'             => 'sale',
            'gateway'          => $gateway,
            'status'           => $status,
            'amount'           => $order->total_price,
            'currency'         => $order->currency,
            'authorization'    => $result['transaction_id'] ?? null,
            'gateway_response' => $result,
            'message'          => $result['message'] ?? null,
            'processed_at'     => now(),
        ]);

        $this->updateOrderFinancialStatus($order->fresh());

        return $transaction;
    }

    public function processRefund(Order $order, float $amount, string $reason = ''): Transaction
    {
        $lastTxn = $order->transactions()->where('status', 'success')->latest()->first();
        $gateway = $lastTxn?->gateway ?? 'manual';

        $gw     = $this->getGateway($gateway);
        $result = $gw->refund($lastTxn?->authorization ?? '', $amount);

        $transaction = Transaction::create([
            'order_id'         => $order->id,
            'parent_id'        => $lastTxn?->id,
            'kind'             => 'refund',
            'gateway'          => $gateway,
            'status'           => ($result['success'] ?? false) ? 'success' : 'failure',
            'amount'           => $amount,
            'currency'         => $order->currency,
            'message'          => $result['message'] ?? $reason,
            'processed_at'     => now(),
        ]);

        $this->updateOrderFinancialStatus($order->fresh());

        return $transaction;
    }

    public function updateOrderFinancialStatus(Order $order): void
    {
        $paid     = $order->transactions()->where('kind', 'sale')->where('status', 'success')->sum('amount');
        $refunded = $order->transactions()->where('kind', 'refund')->where('status', 'success')->sum('amount');
        $total    = (float) $order->total_price;

        $status = match (true) {
            $paid <= 0                          => 'pending',
            $refunded >= $total                 => 'refunded',
            $refunded > 0 && $refunded < $total => 'partially_refunded',
            $paid >= $total                     => 'paid',
            $paid > 0 && $paid < $total         => 'partially_paid',
            default                             => 'pending',
        };

        $order->update(['financial_status' => $status]);
    }
}
