@extends('admin.layouts.app')
@section('title', 'Reports')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <div class="h4 mb-0">Reports</div>
  <div class="d-flex gap-2 align-items-center">
    <input type="text" id="dateRange" class="form-control form-control-sm" placeholder="Date range" style="width:220px;">
    <button class="btn btn-sm btn-primary" onclick="loadAll()">Apply</button>
  </div>
</div>

{{-- Summary stat cards --}}
<div class="row g-3 mb-4" id="summaryCards">
  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-3 p-3 bg-primary bg-opacity-10">
          <i class="fa-solid fa-dollar-sign fa-lg text-primary"></i>
        </div>
        <div>
          <div class="text-secondary small">Total Revenue</div>
          <div class="fw-bold fs-5" id="totalRevenue">—</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-3 p-3 bg-success bg-opacity-10">
          <i class="fa-solid fa-bag-shopping fa-lg text-success"></i>
        </div>
        <div>
          <div class="text-secondary small">Total Orders</div>
          <div class="fw-bold fs-5" id="totalOrders">—</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-3 p-3 bg-info bg-opacity-10">
          <i class="fa-solid fa-receipt fa-lg text-info"></i>
        </div>
        <div>
          <div class="text-secondary small">Avg Order Value</div>
          <div class="fw-bold fs-5" id="avgOrderValue">—</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-3 p-3 bg-warning bg-opacity-10">
          <i class="fa-solid fa-users fa-lg text-warning"></i>
        </div>
        <div>
          <div class="text-secondary small">New Customers</div>
          <div class="fw-bold fs-5" id="newCustomers">—</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  {{-- Sales over time --}}
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="fw-semibold mb-3">Revenue over time</div>
        <canvas id="salesChart" height="90"></canvas>
      </div>
    </div>
  </div>

  {{-- Top products --}}
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="fw-semibold mb-3">Top products by revenue</div>
        <canvas id="productsChart" height="180"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  {{-- Top products table --}}
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="fw-semibold mb-3">Best sellers</div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th><th>Product</th><th>Units sold</th><th>Revenue</th>
              </tr>
            </thead>
            <tbody id="topProductsTable">
              <tr><td colspan="4" class="text-secondary text-center py-3">Loading…</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- New customers chart --}}
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="fw-semibold mb-3">New customers over time</div>
        <canvas id="customersChart" height="180"></canvas>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
var salesChart, productsChart, customersChart;
var startDate = '', endDate = '';

// Init Flatpickr date range
document.addEventListener('DOMContentLoaded', function () {
  if (window.flatpickr) {
    flatpickr('#dateRange', {
      mode: 'range',
      dateFormat: 'Y-m-d',
      defaultDate: [
        new Date(Date.now() - 29 * 86400000).toISOString().slice(0,10),
        new Date().toISOString().slice(0,10)
      ],
      onChange: function(dates) {
        if (dates.length === 2) {
          startDate = dates[0].toISOString().slice(0,10);
          endDate   = dates[1].toISOString().slice(0,10);
        }
      }
    });
  }

  // Set defaults
  var d = new Date();
  endDate   = d.toISOString().slice(0,10);
  d.setDate(d.getDate() - 29);
  startDate = d.toISOString().slice(0,10);

  loadAll();
});

function loadAll() {
  loadSales();
  loadProducts();
  loadCustomers();
}

async function loadSales() {
  const res  = await fetch(`{{ route('admin.reports.sales') }}?start_date=${startDate}&end_date=${endDate}`);
  const data = await res.json();

  document.getElementById('totalRevenue').textContent   = '$' + Number(data.totals?.total_revenue || 0).toFixed(2);
  document.getElementById('totalOrders').textContent    = data.totals?.total_orders || 0;
  document.getElementById('avgOrderValue').textContent  = '$' + Number(data.totals?.avg_order_value || 0).toFixed(2);

  const labels   = data.dates.map(r => r.date);
  const revenues = data.dates.map(r => r.revenue);

  if (salesChart) salesChart.destroy();
  salesChart = new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Revenue ($)',
        data: revenues,
        borderColor: '#5c6ac4',
        backgroundColor: 'rgba(92,106,196,.08)',
        fill: true,
        tension: 0.3,
        pointRadius: 2,
      }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
  });
}

async function loadProducts() {
  const res  = await fetch(`{{ route('admin.reports.products') }}?start_date=${startDate}&end_date=${endDate}`);
  const data = await res.json();

  const rows  = data.products.slice(0, 10);
  const tbody = document.getElementById('topProductsTable');
  tbody.innerHTML = rows.length
    ? rows.map((r, i) => `<tr>
        <td class="text-secondary">${i+1}</td>
        <td>${r.title}</td>
        <td>${r.qty_sold}</td>
        <td class="fw-semibold">$${Number(r.revenue).toFixed(2)}</td>
      </tr>`).join('')
    : '<tr><td colspan="4" class="text-center text-secondary py-3">No data</td></tr>';

  if (productsChart) productsChart.destroy();
  productsChart = new Chart(document.getElementById('productsChart'), {
    type: 'bar',
    data: {
      labels: rows.slice(0,5).map(r => r.title.length > 16 ? r.title.slice(0,16)+'…' : r.title),
      datasets: [{ label: 'Revenue ($)', data: rows.slice(0,5).map(r => r.revenue),
        backgroundColor: '#5c6ac4' }]
    },
    options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
  });
}

async function loadCustomers() {
  const res  = await fetch(`{{ route('admin.reports.customers') }}?start_date=${startDate}&end_date=${endDate}`);
  const data = await res.json();

  const total = data.newCustomers.reduce((s, r) => s + r.count, 0);
  document.getElementById('newCustomers').textContent = total;

  if (customersChart) customersChart.destroy();
  customersChart = new Chart(document.getElementById('customersChart'), {
    type: 'bar',
    data: {
      labels: data.newCustomers.map(r => r.date),
      datasets: [{ label: 'New Customers', data: data.newCustomers.map(r => r.count),
        backgroundColor: '#48bb78' }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
  });
}
</script>
@endpush
