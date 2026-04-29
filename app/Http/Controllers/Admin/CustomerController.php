<?php

namespace App\Http\Controllers\Admin;

use App\Events\CustomerInvited;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(protected CustomerService $service) {}

    public function index(Request $request): View
    {
        $query = Customer::withCount('orders');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) =>
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name',  'like', "%$s%")
                  ->orWhere('email',      'like', "%$s%")
                  ->orWhere('phone',      'like', "%$s%")
            );
        }

        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        if ($request->filled('marketing')) {
            $query->where('accepts_marketing', (bool) $request->marketing);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $sortMap = [
            'name'         => ['first_name', 'asc'],
            'spent-desc'   => ['total_spent', 'desc'],
            'orders-desc'  => ['orders_count', 'desc'],
        ];
        [$col, $dir] = $sortMap[$request->sort] ?? ['id', 'desc'];
        $query->orderBy($col, $dir);

        $customers = $query->paginate(20)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer): View
    {
        $customer->load(['addresses']);
        $orders = $customer->orders()->latest()->paginate(10, ['*'], 'orders_page');

        return view('admin.customers.show', compact('customer', 'orders'));
    }

    public function create(): View
    {
        return view('admin.customers.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['tags']) && is_string($data['tags'])) {
            $data['tags'] = array_values(array_filter(array_map('trim', explode(',', $data['tags']))));
        }

        $customer = $this->service->createCustomer($data);

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Customer created.');
    }

    public function edit(Customer $customer): View
    {
        $customer->load('addresses');
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['tags']) && is_string($data['tags'])) {
            $data['tags'] = array_values(array_filter(array_map('trim', explode(',', $data['tags']))));
        }

        $this->service->updateCustomer($customer, $data);

        return back()->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->orders()->exists()) {
            return back()->with('error', 'Cannot delete customer with existing orders.');
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer deleted.');
    }

    public function sendInvite(Customer $customer): RedirectResponse
    {
        $customer->update(['state' => 'invited']);

        $inviteUrl = url('/account/activate/' . $customer->id . '/' . sha1($customer->email . $customer->created_at));
        CustomerInvited::dispatch($customer, $inviteUrl);

        return back()->with('success', 'Invite sent.');
    }

    public function deactivate(Customer $customer): RedirectResponse
    {
        $customer->update(['state' => 'disabled']);
        return back()->with('success', 'Customer deactivated.');
    }

    public function activate(Customer $customer): RedirectResponse
    {
        $customer->update(['state' => 'enabled']);
        return back()->with('success', 'Customer activated.');
    }
}
