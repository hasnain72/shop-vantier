@extends('admin.layouts.app')
@section('title', 'Refund Order')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <div class="h4 mb-0">Refund — {{ $order->name }}</div>
    <div class="text-secondary small">Paid: ${{ number_format($order->total_price, 2) }}</div>
  </div>
  <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-secondary">Back to order</a>
</div>

<form method="POST" action="{{ route('admin.orders.refunds.store', $order) }}">
  @csrf
  <div class="row g-3">
    <div class="col-md-8">

      {{-- Items to refund --}}
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <div class="fw-semibold mb-3">Items to refund</div>
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr><th>Item</th><th>Ordered</th><th>Qty to refund</th><th>Restock</th></tr>
            </thead>
            <tbody>
              @foreach($order->lineItems as $i => $item)
                <tr>
                  <td>
                    <div class="fw-semibold small">{{ $item->title }}</div>
                    <div class="text-secondary small">{{ $item->variant_title }}</div>
                    <input type="hidden" name="refund_line_items[{{ $i }}][line_item_id]" value="{{ $item->id }}">
                    <input type="hidden" name="refund_line_items[{{ $i }}][variant_id]"   value="{{ $item->variant_id }}">
                  </td>
                  <td>{{ $item->quantity }}</td>
                  <td>
                    <input type="number" name="refund_line_items[{{ $i }}][quantity]"
                      class="form-control form-control-sm refund-qty"
                      style="width:70px;" min="0" max="{{ $item->quantity }}" value="0"
                      data-price="{{ $item->price }}">
                  </td>
                  <td>
                    <select name="refund_line_items[{{ $i }}][restock_type]" class="form-select form-select-sm" style="width:120px;">
                      <option value="no_restock">No restock</option>
                      <option value="return">Return to stock</option>
                    </select>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      {{-- Refund amount --}}
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <div class="fw-semibold mb-3">Refund amount</div>
          <div class="row g-3 align-items-end">
            <div class="col-md-4">
              <label class="form-label">Amount to refund</label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" name="amount" id="refundAmount" step="0.01" min="0.01"
                  max="{{ $order->total_price }}" class="form-control" required
                  value="0.00">
              </div>
            </div>
            <div class="col-md-8">
              <label class="form-label">Reason</label>
              <input name="note" class="form-control" placeholder="Reason for refund…">
            </div>
          </div>
          <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" name="notify_customer" value="1" id="notifyCustomer" checked>
            <label class="form-check-label" for="notifyCustomer">Send refund notification to customer</label>
          </div>
          <div class="form-check mt-1">
            <input class="form-check-input" type="checkbox" name="restock" value="1" id="restockCheck">
            <label class="form-check-label" for="restockCheck">Restock items</label>
          </div>
        </div>
      </div>

    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <div class="fw-semibold mb-2">Summary</div>
          <div class="d-flex justify-content-between small mb-1">
            <span class="text-secondary">Order total</span>
            <span>${{ number_format($order->total_price, 2) }}</span>
          </div>
          <div class="d-flex justify-content-between small mb-1">
            <span class="text-secondary">Previously refunded</span>
            <span>${{ number_format($order->transactions()->where('kind','refund')->sum('amount'), 2) }}</span>
          </div>
          <div class="d-flex justify-content-between fw-semibold">
            <span>Max refundable</span>
            <span>${{ number_format($order->total_price - $order->transactions()->where('kind','refund')->sum('amount'), 2) }}</span>
          </div>
        </div>
      </div>
      <div class="d-grid">
        <button class="btn btn-danger" type="submit">Process refund</button>
      </div>
    </div>
  </div>
</form>
@endsection

@push('scripts')
<script>
  document.querySelectorAll('.refund-qty').forEach(input => {
    input.addEventListener('input', updateRefundAmount);
  });

  function updateRefundAmount() {
    let total = 0;
    document.querySelectorAll('.refund-qty').forEach(input => {
      const qty   = parseInt(input.value) || 0;
      const price = parseFloat(input.dataset.price) || 0;
      total += qty * price;
    });
    document.getElementById('refundAmount').value = total.toFixed(2);
  }
</script>
@endpush
