@extends('admin.layouts.app')
@section('title', 'Orders')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <div class="h4 mb-0">Orders</div>
    <div class="text-secondary small">{{ $orders->total() }} orders total</div>
  </div>
  <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Create order
  </a>
</div>

{{-- Quick filter tabs --}}
<ul class="nav nav-tabs mb-3">
  @php($tab = request('tab', 'all'))
  <li class="nav-item">
    <a class="nav-link {{ $tab === 'all' ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">All</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ $tab === 'unfulfilled' ? 'active' : '' }}"
       href="{{ route('admin.orders.index', ['fulfillment_status' => 'unfulfilled', 'tab' => 'unfulfilled']) }}">
       Unfulfilled
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ $tab === 'unpaid' ? 'active' : '' }}"
       href="{{ route('admin.orders.index', ['financial_status' => 'pending', 'tab' => 'unpaid']) }}">
       Unpaid
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ $tab === 'open' ? 'active' : '' }}"
       href="{{ route('admin.orders.index', ['tab' => 'open']) }}">
       Open
    </a>
  </li>
</ul>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.orders.index') }}">
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
      <div class="row g-2 align-items-end">
        <div class="col-md-3">
          <input type="text" name="search" class="form-control form-control-sm"
            placeholder="Order # or email…" value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
          <select name="financial_status" class="form-select form-select-sm">
            <option value="">All payment</option>
            @foreach(['pending','paid','partially_paid','refunded','voided'] as $s)
              <option value="{{ $s }}" @selected(request('financial_status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <select name="fulfillment_status" class="form-select form-select-sm">
            <option value="">All fulfillment</option>
            <option value="unfulfilled" @selected(request('fulfillment_status') === 'unfulfilled')>Unfulfilled</option>
            <option value="partial" @selected(request('fulfillment_status') === 'partial')>Partial</option>
            <option value="fulfilled" @selected(request('fulfillment_status') === 'fulfilled')>Fulfilled</option>
          </select>
        </div>
        <div class="col-md-2">
          <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
        </div>
        <div class="col-md-2">
          <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
        </div>
        <div class="col-md-1 d-flex gap-1">
          <button class="btn btn-sm btn-primary w-50"><i class="bi bi-search"></i></button>
          <a class="btn btn-sm btn-outline-secondary w-50" href="{{ route('admin.orders.index') }}"><i class="bi bi-x-lg"></i></a>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead class="table-light">
        <tr>
          <th>Order</th>
          <th>Date</th>
          <th>Customer</th>
          <th>Payment</th>
          <th>Fulfillment</th>
          <th>Items</th>
          <th class="text-end">Total</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
          <tr>
            <td>
              <a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold text-decoration-none">
                {{ $order->name ?? '#'.$order->id }}
              </a>
            </td>
            <td class="text-secondary small">{{ $order->created_at->format('M j, Y H:i') }}</td>
            <td class="text-secondary small">
              {{ $order->customer?->full_name ?? $order->email ?? '—' }}
            </td>
            <td>
              <span class="badge text-bg-{{ match($order->financial_status) {
                'paid' => 'success', 'pending' => 'warning', 'refunded' => 'info', default => 'secondary'
              } }}">{{ ucfirst(str_replace('_',' ',$order->financial_status)) }}</span>
            </td>
            <td>
              <span class="badge text-bg-{{ match($order->fulfillment_status) {
                'fulfilled' => 'success', 'partial' => 'warning', default => 'secondary'
              } }}">{{ ucfirst($order->fulfillment_status ?? 'Unfulfilled') }}</span>
            </td>
            <td class="text-secondary small">{{ $order->line_items_count }}</td>
            <td class="text-end fw-semibold">${{ number_format($order->total_price, 2) }}</td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.orders.show', $order) }}">View</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-secondary py-5">No orders found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">{{ $orders->withQueryString()->links() }}</div>
@endsection
