<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\OrderPaid;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\Payment\MyFatoorahGateway;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Handles the browser-return + server-to-server webhook for MyFatoorah.
 *
 * Both entry points end up calling `finalize()` which is idempotent — safe to
 * hit twice (once from user redirect, once from MF webhook).
 */
class PaymentCallbackController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    /**
     * Called by the Angular success page after MF redirects the customer back with ?paymentId=...
     * The Angular route also handles ?paymentId= from the ErrorUrl case (failed payments) —
     * we still verify with MF because they may report success even after landing on ErrorUrl.
     */
    public function myfatoorahReturn(Request $request): JsonResponse
    {
        $paymentId = $request->query('paymentId') ?? $request->input('paymentId');
        if (!$paymentId) {
            return ApiResponse::error('Missing paymentId.', 422);
        }

        $result = $this->finalize($paymentId);
        if (!$result['ok']) {
            return ApiResponse::error($result['message'], 422, ['status' => $result['status']]);
        }

        return ApiResponse::success([
            'order'  => $result['order'] ? new OrderResource($result['order']) : null,
            'status' => $result['status'],
        ], $result['message']);
    }

    /**
     * Server-to-server webhook. MF posts a JSON body with `Data.InvoiceId` (and other fields).
     * We look up the paymentId and re-run finalize(); shared logic ensures idempotency.
     */
    public function myfatoorahWebhook(Request $request): JsonResponse
    {
        $secretHeader = $request->header('MyFatoorah-Signature');
        $expected     = config('payment.myfatoorah.webhook_secret');
        if ($expected && !hash_equals((string) $expected, (string) $secretHeader)) {
            Log::warning('MyFatoorah webhook signature mismatch');
            return ApiResponse::error('Invalid signature.', 401);
        }

        $paymentId = $request->input('Data.PaymentId') ?? $request->input('Data.InvoiceId');
        if (!$paymentId) {
            return ApiResponse::error('Missing payment identifier in webhook body.', 422);
        }

        $this->finalize((string) $paymentId);

        // Webhook must always return 200 to prevent MF from retrying indefinitely.
        return response()->json(['received' => true]);
    }

    /**
     * @return array{ok: bool, status: string, message: string, order: ?Order}
     */
    private function finalize(string $paymentId): array
    {
        $gateway  = new MyFatoorahGateway();
        $lookup   = $gateway->getPaymentStatus($paymentId);
        $body     = $lookup['body']['Data'] ?? [];
        $invoiceId = (string) ($body['InvoiceId'] ?? '');
        $status    = strtoupper((string) ($body['InvoiceStatus'] ?? 'Unknown'));

        if (!$invoiceId) {
            return ['ok' => false, 'status' => $status, 'message' => 'Could not verify payment.', 'order' => null];
        }

        $transaction = Transaction::where('gateway', 'myfatoorah')
            ->where('authorization', $invoiceId)
            ->latest()
            ->first();

        if (!$transaction) {
            return ['ok' => false, 'status' => $status, 'message' => 'Transaction not found.', 'order' => null];
        }

        $order = Order::find($transaction->order_id);
        if (!$order) {
            return ['ok' => false, 'status' => $status, 'message' => 'Order not found.', 'order' => null];
        }

        // Idempotent: don't re-fire OrderPaid if we already finalized this transaction.
        $wasAlreadySuccess = $transaction->status === 'success';

        if ($status === 'PAID') {
            $transaction->update([
                'status'           => 'success',
                'gateway_response' => array_merge((array) $transaction->gateway_response, $body),
                'message'          => 'Payment captured via MyFatoorah.',
                'processed_at'     => now(),
            ]);
            $this->paymentService->updateOrderFinancialStatus($order->fresh());

            if (!$wasAlreadySuccess) {
                OrderPaid::dispatch($order->fresh());
            }

            return ['ok' => true, 'status' => 'PAID', 'message' => 'Payment successful.', 'order' => $order->fresh()];
        }

        if (in_array($status, ['FAILED', 'CANCELED', 'CANCELLED', 'EXPIRED'], true)) {
            $transaction->update([
                'status'           => 'failure',
                'gateway_response' => array_merge((array) $transaction->gateway_response, $body),
                'message'          => $body['TransactionStatus'] ?? $status,
                'processed_at'     => now(),
            ]);
            return ['ok' => true, 'status' => $status, 'message' => 'Payment did not complete.', 'order' => $order->fresh()];
        }

        // Still pending / unknown — leave transaction as-is.
        return ['ok' => true, 'status' => $status, 'message' => 'Payment is still pending.', 'order' => $order->fresh()];
    }
}
