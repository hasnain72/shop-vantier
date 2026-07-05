<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Customer\StoreAddressRequest;
use App\Http\Requests\Api\Customer\UpdateAddressRequest;
use App\Http\Resources\CustomerAddressResource;
use App\Http\Resources\CustomerResource;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(protected CustomerService $service) {}

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(
            new CustomerResource($request->user()->load('addresses'))
        );
    }

    public function updateMe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name'        => ['sometimes', 'string', 'max:255'],
            'last_name'         => ['sometimes', 'string', 'max:255'],
            'phone'             => ['nullable', 'string'],
            'accepts_marketing' => ['sometimes', 'boolean'],
        ]);

        $customer = $this->service->updateCustomer($request->user(), $data);

        return ApiResponse::success(new CustomerResource($customer->load('addresses')), 'Profile updated');
    }

    public function addresses(Request $request): JsonResponse
    {
        $addresses = $request->user()->addresses()->get();

        return ApiResponse::success(CustomerAddressResource::collection($addresses));
    }

    public function storeAddress(StoreAddressRequest $request): JsonResponse
    {
        $customer = $request->user();
        $data     = $request->validated();

        $isFirst = $customer->addresses()->count() === 0;
        $data['customer_id'] = $customer->id;
        $data['is_default']  = $isFirst;

        $address = CustomerAddress::create($data);

        return ApiResponse::success(new CustomerAddressResource($address), 'Address added', 201);
    }

    public function updateAddress(UpdateAddressRequest $request, int $addressId): JsonResponse
    {
        $address = CustomerAddress::where('id', $addressId)
            ->where('customer_id', $request->user()->id)
            ->firstOrFail();

        $address->update($request->validated());

        return ApiResponse::success(new CustomerAddressResource($address), 'Address updated');
    }

    public function destroyAddress(Request $request, int $addressId): JsonResponse
    {
        $customer = $request->user();
        $address  = CustomerAddress::where('id', $addressId)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        if ($address->is_default && $customer->addresses()->count() > 1) {
            return ApiResponse::error('Cannot delete the default address while other addresses exist.', 422);
        }

        $address->delete();

        return ApiResponse::success(null, 'Address deleted');
    }

    public function setDefaultAddress(Request $request, int $addressId): JsonResponse
    {
        $customer = $request->user();

        CustomerAddress::where('id', $addressId)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        $this->service->setDefaultAddress($customer, $addressId);

        return ApiResponse::success(null, 'Default address updated');
    }

    /**
     * Custom order enquiries by this customer. CustomOrder rows don't carry a
     * customer_id — they're keyed by email, so we match on the logged-in
     * customer's email (case-insensitive).
     */
    public function customOrders(Request $request): JsonResponse
    {
        $customer = $request->user();
        $limit    = min((int) ($request->limit ?? 10), 50);

        $customOrders = \App\Models\CustomOrder::whereRaw('LOWER(email) = ?', [strtolower($customer->email)])
            ->latest()
            ->paginate($limit);

        $data = $customOrders->getCollection()->map(fn ($co) => [
            'id'         => $co->id,
            'name'       => $co->name,
            'email'      => $co->email,
            'phone'      => $co->phone,
            'message'    => $co->message,
            'status'     => $co->status,
            'created_at' => $co->created_at?->toISOString(),
        ]);

        return response()->json([
            'success' => true,
            'data'    => $data,
            'meta'    => [
                'pagination' => [
                    'total'        => $customOrders->total(),
                    'per_page'     => $customOrders->perPage(),
                    'current_page' => $customOrders->currentPage(),
                    'last_page'    => $customOrders->lastPage(),
                ],
            ],
        ]);
    }

    public function orders(Request $request): JsonResponse
    {
        $customer = $request->user();
        $limit    = min((int) ($request->limit ?? 10), 50);

        $orders = $customer->orders()
            ->when($request->filled('status'), fn ($q) => $q->where('financial_status', $request->status))
            ->latest()
            ->paginate($limit);

        // Use a lightweight inline transform to avoid circular dependency with OrderResource
        $data = $orders->getCollection()->map(fn ($o) => [
            'id'                 => $o->id,
            'order_number'       => $o->order_number,
            'name'               => $o->name,
            'financial_status'   => $o->financial_status,
            'fulfillment_status' => $o->fulfillment_status,
            'total_price'        => (string) $o->total_price,
            'currency'           => $o->currency,
            'created_at'         => $o->created_at?->toISOString(),
        ]);

        return response()->json([
            'success' => true,
            'data'    => $data,
            'meta'    => [
                'pagination' => [
                    'total'        => $orders->total(),
                    'per_page'     => $orders->perPage(),
                    'current_page' => $orders->currentPage(),
                    'last_page'    => $orders->lastPage(),
                ],
            ],
        ]);
    }
}
