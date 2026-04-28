@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div class="h4 mb-0">Dashboard</div>
  <span class="text-secondary small">{{ now()->format('F j, Y') }}</span>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-secondary small mb-1">Orders Today</div>
            <div class="fs-3 fw-bold">{{ $stats['orders_today'] }}</div>
          </div>
          <span class="bg-primary bg-opacity-10 text-primary rounded p-2"><i class="bi bi-bag fs-5"></i></span>
        </div>
        <div class="text-secondary small mt-2">This month: <strong>{{ $stats['orders_month'] }}</strong></div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-secondary small mb-1">Revenue Today</div>
            <div class="fs-3 fw-bold">${{ number_format($stats['revenue_today'], 2) }}</div>
          </div>
          <span class="bg-success bg-opacity-10 text-success rounded p-2"><i class="bi bi-currency-dollar fs-5"></i></span>
        </div>
        <div class="text-secondary small mt-2">This month: <strong>${{ number_format($stats['revenue_month'], 2) }}</strong></div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-secondary small mb-1">Total Customers</div>
            <div class="fs-3 fw-bold">{{ $stats['customers_total'] }}</div>
          </div>
          <span class="bg-info bg-opacity-10 text-info rounded p-2"><i class="bi bi-people fs-5"></i></span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-secondary small mb-1">Total Products</div>
            <div class="fs-3 fw-bold">{{ $stats['products_total'] }}</div>
          </div>
          <span class="bg-warning bg-opacity-10 text-warning rounded p-2"><i class="bi bi-box fs-5"></i></span>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Revenue Chart --}}
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <div class="fw-semibold mb-3">Revenue — Last 30 Days</div>
    <canvas id="revenueChart" height="100"></canvas>
  </div>
</div>

<div class="row g-3 mb-4">
  {{-- Recent Orders --}}
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="fw-semibold mb-3">Recent Orders</div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Status</th>
                <th class="text-end">Total</th>
                <th class="text-end">Date</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentOrders as $order)
                <tr>
                  <td class="text-secondary">{{ $order->name ?? '#'.$order->id }}</td>
                  <td>{{ $order->customer?->first_name }} {{ $order->customer?->last_name }}</td>
                  <td>
                    <span class="badge text-bg-{{ $order->financial_status === 'paid' ? 'success' : ($order->financial_status === 'pending' ? 'warning' : 'secondary') }}">
                      {{ $order->financial_status }}
                    </span>
                  </td>
                  <td class="text-end">${{ number_format($order->total_price, 2) }}</td>
                  <td class="text-end text-secondary small">{{ $order->created_at->format('M j') }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-secondary py-3">No orders yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Best Sellers --}}
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="fw-semibold mb-3">Best Sellers This Month</div>
        @forelse($bestSellers as $item)
          <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
            <span class="text-truncate me-2" style="max-width:160px;">{{ $item->product?->title ?? 'N/A' }}</span>
            <span class="badge text-bg-primary">{{ $item->total_sold }} sold</span>
          </div>
        @empty
          <div class="text-secondary small py-3">No sales data yet.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>

{{-- Low Stock --}}
<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div class="fw-semibold mb-3 text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Low Stock Alert</div>
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Product</th>
            <th>Variant</th>
            <th>SKU</th>
            <th class="text-end">Qty</th>
          </tr>
        </thead>
        <tbody>
          @forelse($lowStock as $variant)
            <tr>
              <td>
                <a href="{{ route('admin.products.edit', $variant->product_id) }}" class="text-decoration-none fw-semibold">
                  {{ $variant->product?->title ?? 'N/A' }}
                </a>
              </td>
              <td class="text-secondary">{{ $variant->title }}</td>
              <td class="text-secondary small">{{ $variant->sku }}</td>
              <td class="text-end">
                <span class="badge text-bg-{{ $variant->inventory_quantity <= 0 ? 'danger' : 'warning' }}">
                  {{ $variant->inventory_quantity }}
                </span>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-secondary py-3">All products are sufficiently stocked.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const labels = @json(array_keys($chartDates));
  const data   = @json(array_values($chartDates));

  new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Revenue ($)',
        data,
        borderColor: '#0d6efd',
        backgroundColor: 'rgba(13,110,253,0.08)',
        fill: true,
        tension: 0.4,
        pointRadius: 3,
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { callback: v => '$' + v } }
      }
    }
  });
</script>
@endpush
