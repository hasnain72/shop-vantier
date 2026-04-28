<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function createCustomer(array $data): Customer
    {
        return DB::transaction(function () use ($data) {
            $addressData = $data['address'] ?? null;
            unset($data['address']);

            $customer = Customer::create($data);

            if ($addressData) {
                $addressData['customer_id'] = $customer->id;
                $addressData['is_default']  = true;
                CustomerAddress::create($addressData);
            }

            return $customer->fresh(['addresses']);
        });
    }

    public function updateCustomer(Customer $customer, array $data): Customer
    {
        $customer->update($data);
        return $customer->fresh(['addresses']);
    }

    public function setDefaultAddress(Customer $customer, int $addressId): void
    {
        DB::transaction(function () use ($customer, $addressId) {
            CustomerAddress::where('customer_id', $customer->id)->update(['is_default' => false]);
            CustomerAddress::where('id', $addressId)
                ->where('customer_id', $customer->id)
                ->update(['is_default' => true]);
        });
    }

    public function getOrderHistory(Customer $customer, array $filters = []): LengthAwarePaginator
    {
        $query = $customer->orders()->latest();

        if (! empty($filters['status'])) {
            $query->where('financial_status', $filters['status']);
        }

        return $query->paginate($filters['limit'] ?? 10);
    }
}
