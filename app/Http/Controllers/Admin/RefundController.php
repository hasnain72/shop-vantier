<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderRefund;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RefundController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    public function create(Order $order): View
    {
        $order->load(['lineItems.variant.product', 'transactions']);

        return view('admin.refunds.create', compact('order'));
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'amount'          => ['required', 'numeric', 'min:0.01', 'max:' . $order->total_price],
            'note'            => ['nullable', 'string'],
            'restock'         => ['sometimes', 'boolean'],
            'notify_customer' => ['sometimes', 'boolean'],
            'refund_line_items' => ['nullable', 'array'],
        ]);

        $transaction = $this->paymentService->processRefund(
            $order,
            (float) $data['amount'],
            $data['note'] ?? ''
        );

        OrderRefund::create([
            'order_id'          => $order->id,
            'user_id'           => auth()->id(),
            'note'              => $data['note'] ?? null,
            'restock'           => (bool) ($data['restock'] ?? false),
            'refund_line_items' => $data['refund_line_items'] ?? null,
            'transactions'      => [['id' => $transaction->id, 'amount' => $data['amount']]],
        ]);

        if ($data['restock'] ?? false) {
            foreach ($data['refund_line_items'] ?? [] as $rli) {
                \App\Models\ProductVariant::where('id', $rli['variant_id'] ?? null)
                    ->increment('inventory_quantity', (int) ($rli['quantity'] ?? 0));
            }
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Refund of $' . number_format($data['amount'], 2) . ' processed.');
    }
}
