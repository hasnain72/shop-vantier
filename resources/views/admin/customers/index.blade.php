@extends('admin.layouts.app')
@section('title', 'Customers')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <div class="h4 mb-0">Customers</div>
    <div class="text-secondary small">{{ $customers->total() }} customers total</div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.customers.index', array_merge(request()->query(), ['export' => 1])) }}"
       class="btn btn-outline-secondary"><i class="bi bi-download me-1"></i>Export CSV</a>
    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-lg me-1"></i>New customer
    </a>
  </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.customers.index') }}">
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
      <div class="row g-2 align-items-end">
        <div class="col-md-3">
          <input type="text" name="search" class="form-control form-control-sm"
            placeholder="Name, email, phone…" value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
          <select name="state" class="form-select form-select-sm">
            <option value="">All states</option>
            @foreach(['enabled','disabled','invited','declined'] as $s)
              <option value="{{ $s }}" @selected(request('state') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <select name="marketing" class="form-select form-select-sm">
            <option value="">All marketing</option>
            <option value="1" @selected(request('marketing') === '1')>Subscribed</option>
            <option value="0" @selected(request('marketing') === '0')>Not subscribed</option>
          </select>
        </div>
        <div class="col-md-2">
          <input type="date" name="from" class="form-control form-control-sm"
            value="{{ request('from') }}" placeholder="From">
        </div>
        <div class="col-md-2">
          <input type="date" name="to" class="form-control form-control-sm"
            value="{{ request('to') }}" placeholder="To">
        </div>
        <div class="col-md-1 d-flex gap-1">
          <button class="btn btn-sm btn-primary w-50" type="submit"><i class="bi bi-search"></i></button>
          <a class="btn btn-sm btn-outline-secondary w-50" href="{{ route('admin.customers.index') }}"><i class="bi bi-x-lg"></i></a>
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
          <th>Name</th>
          <th>Email</th>
          <th>Location</th>
          <th>Orders</th>
          <th>Total Spent</th>
          <th>State</th>
          <th>Marketing</th>
          <th>Joined</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($customers as $customer)
          <tr>
            <td>
              <a href="{{ route('admin.customers.show', $customer) }}" class="fw-semibold text-decoration-none">
                {{ $customer->full_name }}
              </a>
            </td>
            <td class="text-secondary small">{{ $customer->email }}</td>
            <td class="text-secondary small">
              {{ $customer->defaultAddress?->city }}
              {{ $customer->defaultAddress?->country_code }}
            </td>
            <td>{{ $customer->orders_count }}</td>
            <td>${{ number_format($customer->total_spent, 2) }}</td>
            <td>
              <span class="badge text-bg-{{ $customer->state === 'enabled' ? 'success' : ($customer->state === 'disabled' ? 'danger' : 'warning') }}">
                {{ ucfirst($customer->state) }}
              </span>
            </td>
            <td>
              @if($customer->accepts_marketing)
                <span class="badge text-bg-info">Subscribed</span>
              @else
                <span class="text-secondary small">—</span>
              @endif
            </td>
            <td class="text-secondary small">{{ $customer->created_at->format('M j, Y') }}</td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.customers.show', $customer) }}">View</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center text-secondary py-5">No customers found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">{{ $customers->withQueryString()->links() }}</div>
@endsection
