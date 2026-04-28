@extends('admin.layouts.app')
@section('title', $customer->full_name)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <div class="h4 mb-0">{{ $customer->full_name }}</div>
    <div class="text-secondary small">Customer since {{ $customer->created_at->format('F j, Y') }}</div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-outline-secondary">Edit</a>
    @if($customer->state === 'enabled')
      <form method="POST" action="{{ route('admin.customers.deactivate', $customer) }}">
        @csrf
        <button class="btn btn-outline-warning btn-sm">Deactivate</button>
      </form>
    @else
      <form method="POST" action="{{ route('admin.customers.activate', $customer) }}">
        @csrf
        <button class="btn btn-outline-success btn-sm">Activate</button>
      </form>
    @endif
    <form method="POST" action="{{ route('admin.customers.invite', $customer) }}">
      @csrf
      <button class="btn btn-outline-secondary btn-sm">Send Invite</button>
    </form>
  </div>
</div>

{{-- Header card --}}
<div class="row g-3 mb-3">
  <div class="col-md-8">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex gap-3 align-items-start">
          <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
            style="width:56px;height:56px;font-size:22px;flex-shrink:0;">
            {{ strtoupper(substr($customer->first_name, 0, 1)) }}
          </div>
          <div>
            <div class="fw-semibold fs-5">{{ $customer->full_name }}
              <span class="badge ms-1 text-bg-{{ $customer->state === 'enabled' ? 'success' : 'secondary' }}">{{ $customer->state }}</span>
            </div>
            <div class="text-secondary">{{ $customer->email }}</div>
            @if($customer->phone)<div class="text-secondary">{{ $customer->phone }}</div>@endif
            @if($customer->accepts_marketing)
              <span class="badge text-bg-info mt-1">Email marketing subscribed</span>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="row g-3 text-center">
          <div class="col-4">
            <div class="fs-4 fw-bold">{{ $customer->orders_count }}</div>
            <div class="text-secondary small">Orders</div>
          </div>
          <div class="col-4">
            <div class="fs-4 fw-bold">${{ number_format($customer->total_spent, 0) }}</div>
            <div class="text-secondary small">Spent</div>
          </div>
          <div class="col-4">
            <div class="fs-4 fw-bold">
              ${{ $customer->orders_count > 0 ? number_format($customer->total_spent / $customer->orders_count, 0) : '0' }}
            </div>
            <div class="text-secondary small">Avg Order</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-8">

    {{-- Orders --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">Order history</div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Order</th>
                <th>Date</th>
                <th>Payment</th>
                <th>Fulfillment</th>
                <th class="text-end">Total</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders as $order)
                <tr>
                  <td><a href="{{ route('admin.orders.show', $order) }}" class="text-decoration-none">{{ $order->name ?? '#'.$order->id }}</a></td>
                  <td class="text-secondary small">{{ $order->created_at->format('M j, Y') }}</td>
                  <td>
                    <span class="badge text-bg-{{ $order->financial_status === 'paid' ? 'success' : 'warning' }}">
                      {{ $order->financial_status }}
                    </span>
                  </td>
                  <td>
                    <span class="badge text-bg-secondary">
                      {{ $order->fulfillment_status ?? 'unfulfilled' }}
                    </span>
                  </td>
                  <td class="text-end">${{ number_format($order->total_price, 2) }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-secondary py-3">No orders yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="mt-2">{{ $orders->links() }}</div>
      </div>
    </div>

  </div>
  <div class="col-md-4">

    {{-- Addresses --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="fw-semibold">Addresses</div>
        </div>
        @forelse($customer->addresses as $addr)
          <div class="border rounded p-2 mb-2 small">
            @if($addr->is_default)<span class="badge text-bg-secondary mb-1">Default</span>@endif
            <div>{{ $addr->first_name }} {{ $addr->last_name }}</div>
            <div class="text-secondary">{{ $addr->address1 }}</div>
            <div class="text-secondary">{{ $addr->city }}, {{ $addr->country }}</div>
          </div>
        @empty
          <div class="text-secondary small">No addresses on file.</div>
        @endforelse
      </div>
    </div>

    {{-- Notes & Tags --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-2">Notes</div>
        <div class="text-secondary small">{{ $customer->note ?: 'No notes.' }}</div>
        <div class="fw-semibold mt-3 mb-2">Tags</div>
        @forelse($customer->tags ?? [] as $tag)
          <span class="badge text-bg-light text-dark border me-1">{{ $tag }}</span>
        @empty
          <div class="text-secondary small">No tags.</div>
        @endforelse
      </div>
    </div>

  </div>
</div>
@endsection
