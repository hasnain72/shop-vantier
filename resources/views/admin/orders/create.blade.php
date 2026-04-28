@extends('admin.layouts.app')
@section('title', 'Create Order')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Create order</div>
  <a class="btn btn-outline-secondary" href="{{ route('admin.orders.index') }}">Back</a>
</div>

<form method="POST" action="{{ route('admin.orders.store') }}" id="orderForm">
  @csrf
  <div class="row g-3">
    <div class="col-md-8">

      {{-- Product search --}}
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <div class="fw-semibold mb-2">Add products</div>
          <input type="text" id="productSearch" class="form-control mb-3" placeholder="Search products…">
          <div id="productResults" class="list-group mb-3" style="max-height:200px;overflow-y:auto;"></div>
          <div id="lineItemsContainer">
            <div class="text-secondary small">No items added yet.</div>
          </div>
        </div>
      </div>

      {{-- Shipping address --}}
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <div class="fw-semibold mb-3">Shipping address</div>
          <div class="row g-2">
            <div class="col-md-6">
              <input name="shipping_address[first_name]" class="form-control" placeholder="First name">
            </div>
            <div class="col-md-6">
              <input name="shipping_address[last_name]" class="form-control" placeholder="Last name">
            </div>
            <div class="col-12">
              <input name="shipping_address[address1]" class="form-control" placeholder="Address">
            </div>
            <div class="col-md-4">
              <input name="shipping_address[city]" class="form-control" placeholder="City">
            </div>
            <div class="col-md-4">
              <input name="shipping_address[zip]" class="form-control" placeholder="ZIP">
            </div>
            <div class="col-md-4">
              <input name="shipping_address[country]" class="form-control" placeholder="Country">
            </div>
          </div>
        </div>
      </div>

      {{-- Discount & Note --}}
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Discount code</label>
              <input name="discount_code" class="form-control" placeholder="SUMMER20">
            </div>
            <div class="col-md-6">
              <label class="form-label">Note</label>
              <input name="note" class="form-control" placeholder="Order notes…">
            </div>
          </div>
        </div>
      </div>

    </div>
    <div class="col-md-4">

      {{-- Customer --}}
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <div class="fw-semibold mb-2">Customer</div>
          <input type="text" id="customerSearch" class="form-control mb-2" placeholder="Search customer…">
          <div id="customerResults" class="list-group mb-2"></div>
          <div id="selectedCustomer" class="text-secondary small"></div>
          <input type="hidden" name="customer_id" id="customerIdInput">
          <div class="mt-2">
            <label class="form-label small">Email (for guest)</label>
            <input name="email" type="email" class="form-control form-control-sm">
          </div>
        </div>
      </div>

      <div class="d-grid">
        <button class="btn btn-primary" type="submit">Create order</button>
      </div>
    </div>
  </div>
</form>
@endsection

@push('scripts')
<script>
  const variants = @json($products->pluck('variants')->flatten()->map(fn($v) => [
    'id' => $v->id,
    'title' => $v->product->title . ' — ' . $v->title,
    'price' => $v->price,
    'sku' => $v->sku,
  ]));

  let lineItems = [];

  document.getElementById('productSearch').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    const results = variants.filter(v => v.title.toLowerCase().includes(q));
    const container = document.getElementById('productResults');
    container.innerHTML = results.slice(0, 8).map(v =>
      `<button type="button" class="list-group-item list-group-item-action small py-1"
         onclick="addItem(${v.id}, '${v.title.replace(/'/g,"\\'")}', ${v.price})">
         <strong>${v.title}</strong> — $${v.price}
       </button>`
    ).join('');
  });

  function addItem(id, title, price) {
    const existing = lineItems.find(i => i.variant_id === id);
    if (existing) { existing.quantity++; }
    else { lineItems.push({ variant_id: id, quantity: 1, title, price }); }
    renderItems();
  }

  function removeItem(id) {
    lineItems = lineItems.filter(i => i.variant_id !== id);
    renderItems();
  }

  function renderItems() {
    const c = document.getElementById('lineItemsContainer');
    if (!lineItems.length) { c.innerHTML = '<div class="text-secondary small">No items added yet.</div>'; return; }
    c.innerHTML = `<table class="table table-sm">
      <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th></th></tr></thead>
      <tbody>${lineItems.map((item, idx) => `
        <tr>
          <td>${item.title}<input type="hidden" name="line_items[${idx}][variant_id]" value="${item.variant_id}"></td>
          <td><input type="number" name="line_items[${idx}][quantity]" value="${item.quantity}" min="1" class="form-control form-control-sm" style="width:70px;" onchange="updateQty(${item.variant_id}, this.value)"></td>
          <td>$${(item.price * item.quantity).toFixed(2)}</td>
          <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(${item.variant_id})">×</button></td>
        </tr>`).join('')}
      </tbody></table>`;
  }

  function updateQty(id, qty) {
    const item = lineItems.find(i => i.variant_id === id);
    if (item) item.quantity = parseInt(qty) || 1;
  }
</script>
@endpush
