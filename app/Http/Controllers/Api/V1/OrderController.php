<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\OrderCancelled;
use App\Events\OrderCreated;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderService $service) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_id'    => ['nullable', 'exists:customers,id'],
            'email'          => ['nullable', 'email'],
            'phone'          => ['nullable', 'string'],
            'line_items'     => ['required', 'array', 'min:1'],
            'line_items.*.variant_id' => ['required', 'exists:product_variants,id'],
            'line_items.*.quantity'   => ['required', 'integer', 'min:1'],
            'line_items.*.properties' => ['nullable', 'array'],
            'shipping_address' => ['required', 'array'],
            'billing_address'  => ['nullable', 'array'],
            'discount_code'    => ['nullable', 'string'],
            'note'             => ['nullable', 'string'],
            'buyer_accepts_marketing' => ['sometimes', 'boolean'],
        ]);

        if (! $this->service->validateInventory($data['line_items'])) {
            return ApiResponse::error('One or more items are out of stock.', 422);
        }

        $order = $this->service->createOrder($data);

        OrderCreated::dispatch($order);

        return ApiResponse::success(
            ['order' => new OrderResource($order->load(['lineItems', 'customer', 'transactions', 'fulfillments']))],
            'Order created',
            201
        );
    }

    public function show(Request $request, string $identifier): JsonResponse
    {
        $order = Order::with(['lineItems.variant.product', 'customer', 'transactions', 'fulfillments'])
            ->where(fn ($q) =>
                is_numeric($identifier)
                    ? $q->where('id', $identifier)
                    : $q->where('order_number', $identifier)
            )
            ->when($request->user(), fn ($q, $customer) => $q->where('customer_id', $customer->id))
            ->firstOrFail();

        return ApiResponse::success(['order' => new OrderResource($order)]);
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        $customer = $request->user();

        if ($customer && $order->customer_id !== $customer->id) {
            return ApiResponse::error('Not found.', 404);
        }

        if (! in_array($order->financial_status, ['pending']) || $order->fulfillment_status) {
            return ApiResponse::error('This order cannot be cancelled.', 422);
        }

        $this->service->cancelOrder($order, 'customer', false);

        OrderCancelled::dispatch($order->fresh());

        return ApiResponse::success(['order' => new OrderResource($order->fresh())], 'Order cancelled');
    }
}
