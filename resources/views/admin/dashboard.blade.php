@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
  <div class="row g-3 mb-3">
    <div class="col-md-3">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="text-secondary small">Orders Today</div>
          <div class="fs-4 fw-semibold">{{ $stats['orders_today'] }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="text-secondary small">Revenue Today</div>
          <div class="fs-4 fw-semibold">${{ number_format($stats['revenue_today'], 2) }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="text-secondary small">Total Customers</div>
          <div class="fs-4 fw-semibold">{{ $stats['customers_total'] }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="text-secondary small">Total Products</div>
          <div class="fs-4 fw-semibold">{{ $stats['products_total'] }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="fw-semibold mb-2">Getting started</div>
      <div class="text-secondary">
        Next: run migrations for your Shopify-style schema, then wire real stats into this dashboard.
      </div>
    </div>
  </div>
@endsection

