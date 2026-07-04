<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * MyFatoorah gateway (Kuwait / MENA card + wallet payments).
 *
 * Flow: charge() calls MF ExecutePayment with the pending order's data. MF returns
 * a hosted PaymentURL, which we bubble up as gateway_response.redirect_url so
 * PaymentService writes a `pending` Transaction. When the customer returns,
 * PaymentCallbackController verifies via GetPaymentStatus and finalizes.
 */
class MyFatoorahGateway implements PaymentGatewayInterface
{
    public function getGatewayName(): string
    {
        return 'myfatoorah';
    }

    public function charge(array $data): array
    {
        $config = config('payment.myfatoorah');

        if (empty($config['api_key'])) {
            return [
                'success' => false,
                'pending' => false,
                'message' => 'MyFatoorah API key not configured.',
            ];
        }

        $currency = $data['currency'] ?: $config['default_currency'];
        $orderId  = $data['order_id'];
        // Callers may pass their own callback URLs (e.g. per-request), else fall back to config.
        $returnUrl = $data['return_url'] ?? $config['return_url'];
        $errorUrl  = $data['error_url']  ?? $config['error_url'];

        // ExecutePayment requires a PaymentMethodId. 0 = "show hosted method-selection page" (safest default).
        $paymentMethodId = (int) ($data['payment_method_id'] ?? 0);

        $payload = [
            'PaymentMethodId'  => $paymentMethodId,
            'CustomerName'     => $data['customer_name']  ?? 'Customer',
            'CustomerEmail'    => $data['customer_email'] ?? null,
            'CustomerMobile'   => $data['customer_mobile'] ?? null,
            'MobileCountryCode'=> $data['customer_country_code'] ?? null,
            'DisplayCurrencyIso' => $currency,
            'InvoiceValue'     => (float) $data['amount'],
            'CallBackUrl'      => $returnUrl,
            'ErrorUrl'         => $errorUrl,
            'Language'         => $data['language'] ?? 'en',
            'CustomerReference'=> (string) $orderId,
            'UserDefinedField' => (string) $orderId, // echoed back on callback for extra safety
        ];

        try {
            $response = Http::withToken($config['api_key'])
                ->acceptJson()
                ->timeout(20)
                ->post(rtrim($config['base_url'], '/') . '/v2/ExecutePayment', $payload);

            $body = $response->json() ?? [];

            if (!$response->ok() || !($body['IsSuccess'] ?? false)) {
                Log::warning('MyFatoorah ExecutePayment failed', ['status' => $response->status(), 'body' => $body]);
                return [
                    'success' => false,
                    'pending' => false,
                    'message' => $body['Message'] ?? 'MyFatoorah request failed.',
                    'gateway_response' => $body,
                ];
            }

            $mfData = $body['Data'] ?? [];

            return [
                'success'        => false,
                'pending'        => true,
                'transaction_id' => (string) ($mfData['InvoiceId'] ?? ''),
                'redirect_url'   => $mfData['PaymentURL'] ?? null,
                'message'        => 'Redirect required.',
                'gateway_response' => $mfData,
            ];
        } catch (\Throwable $e) {
            Log::error('MyFatoorah ExecutePayment exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'pending' => false,
                'message' => 'MyFatoorah unreachable: ' . $e->getMessage(),
            ];
        }
    }

    /** Look up the current MF status for a paymentId returned in the browser callback. */
    public function getPaymentStatus(string $paymentId): array
    {
        $config = config('payment.myfatoorah');

        try {
            $response = Http::withToken($config['api_key'])
                ->acceptJson()
                ->timeout(20)
                ->post(rtrim($config['base_url'], '/') . '/v2/GetPaymentStatus', [
                    'Key'  => $paymentId,
                    'KeyType' => 'PaymentId',
                ]);

            return [
                'ok'   => $response->ok() && (($response->json('IsSuccess')) ?? false),
                'body' => $response->json() ?? [],
            ];
        } catch (\Throwable $e) {
            Log::error('MyFatoorah GetPaymentStatus exception', ['error' => $e->getMessage()]);
            return ['ok' => false, 'body' => ['Message' => $e->getMessage()]];
        }
    }

    public function refund(string $transactionId, float $amount): array
    {
        $config = config('payment.myfatoorah');

        try {
            $response = Http::withToken($config['api_key'])
                ->acceptJson()
                ->timeout(20)
                ->post(rtrim($config['base_url'], '/') . '/v2/MakeRefund', [
                    'Key'         => $transactionId,
                    'KeyType'     => 'InvoiceId',
                    'RefundChargeOnCustomer' => false,
                    'ServiceChargeOnCustomer' => 0,
                    'Amount'      => $amount,
                    'Comment'     => 'Storefront refund',
                    'AmountDeductedFromSupplier' => 0,
                ]);

            $body = $response->json() ?? [];

            return [
                'success' => $response->ok() && ($body['IsSuccess'] ?? false),
                'message' => $body['Message'] ?? null,
                'gateway_response' => $body,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function void(string $transactionId): array
    {
        // MyFatoorah doesn't expose a separate "void" — use refund with full amount before capture.
        return [
            'success' => false,
            'message' => 'MyFatoorah does not support void. Use refund instead.',
        ];
    }
}
