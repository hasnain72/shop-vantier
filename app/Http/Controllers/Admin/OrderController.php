<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderCancelled;
use App\Events\OrderCreated;
use App\Events\OrderFulfilled;
use App\Events\OrderPaid;
use App\Events\OrderStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(protected OrderService $service) {}

    public function index(Request $request): View
    {
        $query = Order::with('customer')->withCount('lineItems');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) =>
                $q->where('order_number', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhereHas('customer', fn ($c) =>
                      $c->where('email', 'like', "%$s%")
                   )
            );
        }

        if ($request->filled('financial_status')) {
            $query->where('financial_status', $request->financial_status);
        }

        if ($request->filled('fulfillment_status')) {
            if ($request->fulfillment_status === 'unfulfilled') {
                $query->whereNull('fulfillment_status');
            } else {
                $query->where('fulfillment_status', $request->fulfillment_status);
            }
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $sortMap = [
            'oldest'      => ['id', 'asc'],
            'total-desc'  => ['total_price', 'desc'],
            'total-asc'   => ['total_price', 'asc'],
        ];
        [$col, $dir] = $sortMap[$request->sort] ?? ['id', 'desc'];
        $query->orderBy($col, $dir);

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load([
            'lineItems.variant.product',
            'customer.addresses',
            'fulfillments',
            'refunds',
            'transactions',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function create(): View
    {
        $products = Product::where('status', 'active')->with('variants')->get();
        return view('admin.orders.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id'    => ['nullable', 'exists:customers,id'],
            'email'          => ['nullable', 'email'],
            'line_items'     => ['required', 'array', 'min:1'],
            'line_items.*.variant_id' => ['required', 'exists:product_variants,id'],
            'line_items.*.quantity'   => ['required', 'integer', 'min:1'],
            'shipping_address' => ['nullable', 'array'],
            'discount_code'  => ['nullable', 'string'],
            'note'           => ['nullable', 'string'],
        ]);

        $order = $this->service->createOrder($data);

        OrderCreated::dispatch($order);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order created.');
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'shipping_address' => ['nullable', 'array'],
            'billing_address'  => ['nullable', 'array'],
            'note'             => ['nullable', 'string'],
            'tags'             => ['nullable', 'array'],
        ]);

        $order->update($data);

        return back()->with('success', 'Order updated.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        if (! in_array($order->financial_status, ['pending', 'voided']) || $order->fulfillment_status) {
            return back()->with('error', 'This order cannot be deleted.');
        }

        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted.');
    }

    public function cancelOrder(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'reason'            => ['required', 'in:customer,fraud,inventory,declined,other'],
            'restock'           => ['sometimes', 'boolean'],
            'send_notification' => ['sometimes', 'boolean'],
        ]);

        $this->service->cancelOrder($order, $data['reason'], (bool) ($data['restock'] ?? false));

        OrderCancelled::dispatch($order->fresh());

        return back()->with('success', 'Order cancelled.');
    }

    public function fulfillOrder(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'location_id'       => ['nullable', 'exists:inventory_locations,id'],
            'tracking_number'   => ['nullable', 'string'],
            'tracking_company'  => ['nullable', 'string'],
            'tracking_url'      => ['nullable', 'url'],
            'notify_customer'   => ['sometimes', 'boolean'],
            'shipping_provider' => ['nullable', 'in:manual,smsa'],
        ]);

        // If admin picked SMSA, call the courier API before we create the fulfillment
        // row so the returned AWB lands directly on the fulfillment record.
        if (($data['shipping_provider'] ?? 'manual') === 'smsa') {
            $provider = app(\App\Services\ShippingProviderService::class)->get('smsa');
            $result   = $provider->createShipment($order);

            if (!$result['success']) {
                return back()->with('error', 'SMSA shipment failed: ' . ($result['message'] ?? 'Unknown error.'));
            }

            $data['tracking_number']  = $result['tracking_number'];
            $data['tracking_url']     = $result['tracking_url'];
            $data['tracking_company'] = 'SMSA Express';
        }

        $fulfillment = $this->service->fulfillOrder($order, $data);

        if ($fulfillment) {
            OrderFulfilled::dispatch($order->fresh(), $fulfillment);
        }

        return back()->with('success', 'Order fulfilled.');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'financial_status'   => 'nullable|in:pending,authorized,partially_paid,paid,partially_refunded,refunded,voided',
            'fulfillment_status' => 'nullable|in:unfulfilled,partial,fulfilled',
            'notify_customer'    => 'nullable|boolean',
        ]);

        $newFinancial   = $data['financial_status']   ?? $order->financial_status;
        $newFulfillment = $data['fulfillment_status'] ?? null;
        // Normalize "unfulfilled" back to null since the DB column is nullable.
        if ($newFulfillment === 'unfulfilled') {
            $newFulfillment = null;
        }
        // If the fulfillment field wasn't in the request at all, keep the current value.
        if (!array_key_exists('fulfillment_status', $data)) {
            $newFulfillment = $order->fulfillment_status;
        }

        $changes = [];
        if ($newFinancial !== $order->financial_status) {
            $changes['financial_status'] = ['old' => $order->financial_status, 'new' => $newFinancial];
        }
        if ($newFulfillment !== $order->fulfillment_status) {
            $changes['fulfillment_status'] = ['old' => $order->fulfillment_status, 'new' => $newFulfillment];
        }

        if (empty($changes)) {
            return back()->with('info', 'Nothing to update.');
        }

        $order->update([
            'financial_status'   => $newFinancial,
            'fulfillment_status' => $newFulfillment,
        ]);

        $notify = (bool) ($data['notify_customer'] ?? false);

        OrderStatusChanged::dispatch($order->fresh(), $changes, $notify);

        // If admin manually flipped payment to paid via this endpoint, also fire the
        // payment-received email so the customer gets the receipt-style notice.
        if (isset($changes['financial_status']) && $changes['financial_status']['new'] === 'paid' && $notify) {
            OrderPaid::dispatch($order->fresh());
        }

        return back()->with('success', 'Order status updated.');
    }

    public function markAsPaid(Order $order): RedirectResponse
    {
        \App\Models\Transaction::create([
            'order_id'     => $order->id,
            'kind'         => 'sale',
            'gateway'      => 'manual',
            'status'       => 'success',
            'amount'       => $order->total_price,
            'currency'     => $order->currency,
            'processed_at' => now(),
        ]);

        $order->update(['financial_status' => 'paid']);

        OrderPaid::dispatch($order->fresh());

        return back()->with('success', 'Order marked as paid.');
    }

    public function archiveOrder(Order $order): RedirectResponse
    {
        $order->update(['closed_at' => now()]);
        return back()->with('success', 'Order archived.');
    }

    public function unarchiveOrder(Order $order): RedirectResponse
    {
        $order->update(['closed_at' => null]);
        return back()->with('success', 'Order unarchived.');
    }

    public function printOrder(Order $order): Response
    {
        $order->load(['lineItems.variant.product', 'customer', 'transactions']);
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }
}
