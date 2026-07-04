@extends('admin.layouts.app')
@section('title', 'Order ' . ($order->name ?? $order->id))

@section('content')
<div class="d-flex align-items-start justify-content-between mb-3 flex-wrap gap-2">
  <div>
    <div class="h4 mb-0">{{ $order->name ?? '#'.$order->id }}</div>
    <div class="text-secondary small">{{ $order->created_at->format('F j, Y \a\t H:i') }}</div>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#statusModal">
      <i class="bi bi-arrow-repeat me-1"></i>Change status
    </button>
    @if(!$order->fulfillment_status)
      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#fulfillModal">Fulfill</button>
    @endif
    @if(!$order->cancelled_at)
      <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancel</button>
    @endif
    @if($order->financial_status === 'pending')
      <form method="POST" action="{{ route('admin.orders.mark-paid', $order) }}">
        @csrf
        <button class="btn btn-sm btn-outline-success">Mark as paid</button>
      </form>
    @endif
    <a href="{{ route('admin.orders.print', $order) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
      <i class="bi bi-printer me-1"></i>Print
    </a>
    @if(!$order->closed_at)
      <form method="POST" action="{{ route('admin.orders.archive', $order) }}">
        @csrf
        <button class="btn btn-sm btn-outline-secondary">Archive</button>
      </form>
    @else
      <form method="POST" action="{{ route('admin.orders.unarchive', $order) }}">
        @csrf
        <button class="btn btn-sm btn-outline-secondary">Unarchive</button>
      </form>
    @endif
  </div>
</div>

<div class="row g-3">
  {{-- LEFT --}}
  <div class="col-md-8">

    {{-- Line Items --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="fw-semibold">Items</div>
          <span class="badge text-bg-{{ $order->fulfillment_status === 'fulfilled' ? 'success' : 'secondary' }}">
            {{ ucfirst($order->fulfillment_status ?? 'Unfulfilled') }}
          </span>
        </div>
        <table class="table table-sm align-middle mb-0">
          <tbody>
            @foreach($order->lineItems as $item)
              <tr>
                <td style="width:48px;">
                  @if($item->variant?->product?->featured_image)
                    <img src="{{ asset('storage/'.$item->variant->product->featured_image) }}"
                      class="rounded" width="40" height="40" style="object-fit:cover;">
                  @else
                    <div class="bg-light rounded" style="width:40px;height:40px;"></div>
                  @endif
                </td>
                <td>
                  <div class="fw-semibold small">{{ $item->title }}</div>
                  <div class="text-secondary small">{{ $item->variant_title }} — SKU: {{ $item->sku }}</div>
                </td>
                <td class="text-secondary small">${{ number_format($item->price, 2) }} × {{ $item->quantity }}</td>
                <td class="text-end fw-semibold">${{ number_format($item->price * $item->quantity, 2) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <hr>
        <div class="d-flex justify-content-end">
          <table class="table table-sm w-auto">
            <tr><td class="text-secondary pe-4">Subtotal</td><td class="text-end">${{ number_format($order->subtotal_price, 2) }}</td></tr>
            @if($order->total_discounts > 0)
              <tr><td class="text-success pe-4">Discount</td><td class="text-end text-success">-${{ number_format($order->total_discounts, 2) }}</td></tr>
            @endif
            <tr><td class="text-secondary pe-4">Shipping</td><td class="text-end">${{ number_format($order->total_shipping, 2) }}</td></tr>
            <tr><td class="text-secondary pe-4">Tax</td><td class="text-end">${{ number_format($order->total_tax, 2) }}</td></tr>
            <tr class="fw-semibold"><td class="pe-4">Total</td><td class="text-end fs-5">${{ number_format($order->total_price, 2) }}</td></tr>
          </table>
        </div>
      </div>
    </div>

    {{-- Payment --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="fw-semibold">Payment</div>
          <span class="badge text-bg-{{ match($order->financial_status) { 'paid' => 'success', 'pending' => 'warning', default => 'secondary' } }}">
            {{ ucfirst(str_replace('_',' ',$order->financial_status)) }}
          </span>
        </div>
        @forelse($order->transactions as $txn)
          <div class="d-flex justify-content-between align-items-center border-bottom py-2 small">
            <div>
              <span class="fw-semibold">{{ ucfirst($txn->kind) }}</span>
              <span class="text-secondary ms-2">{{ $txn->gateway }}</span>
            </div>
            <div>
              <span class="badge text-bg-{{ $txn->status === 'success' ? 'success' : 'warning' }} me-2">{{ $txn->status }}</span>
              <span class="fw-semibold">${{ number_format($txn->amount, 2) }}</span>
            </div>
          </div>
        @empty
          <div class="text-secondary small">No transactions recorded.</div>
        @endforelse
      </div>
    </div>

    {{-- Fulfillment --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-2">Fulfillment</div>
        @forelse($order->fulfillments as $fulfillment)
          <div class="border rounded p-3 mb-2 small">
            <div class="d-flex justify-content-between align-items-center">
              <span class="fw-semibold">{{ ucfirst($fulfillment->status) }}</span>
              <span class="text-secondary">{{ $fulfillment->created_at->format('M j, Y') }}</span>
            </div>
            @if($fulfillment->tracking_number)
              <div class="mt-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                  <span class="text-secondary">{{ $fulfillment->tracking_company }}</span>
                  <code class="bg-light px-2 py-1 rounded">{{ $fulfillment->tracking_number }}</code>
                  <a href="{{ $fulfillment->getTrackingUrl() }}" target="_blank"
                    class="btn btn-xs btn-outline-secondary btn-sm py-0 px-2">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Track
                  </a>
                </div>
                @if($fulfillment->shipment_status)
                  <div class="mt-1">
                    <span class="badge text-bg-info">{{ str_replace('_',' ', $fulfillment->shipment_status) }}</span>
                  </div>
                @endif
              </div>
            @endif
            @if($fulfillment->location)
              <div class="text-secondary mt-1">Fulfilled from: {{ $fulfillment->location->name }}</div>
            @endif
          </div>
        @empty
          <div class="text-secondary small">Not yet fulfilled.</div>
        @endforelse
      </div>
    </div>

  </div>

  {{-- RIGHT --}}
  <div class="col-md-4">

    {{-- Customer --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-2">Customer</div>
        @if($order->customer)
          <a href="{{ route('admin.customers.show', $order->customer) }}" class="text-decoration-none fw-semibold">
            {{ $order->customer->full_name }}
          </a>
          <div class="text-secondary small">{{ $order->customer->email }}</div>
          <div class="text-secondary small">{{ $order->customer->orders_count }} orders</div>
        @else
          <div class="text-secondary small">{{ $order->email ?? 'Guest' }}</div>
        @endif

        @if($order->shipping_address)
          <div class="fw-semibold mt-3 mb-1 small">Shipping address</div>
          <div class="text-secondary small">
            {{ $order->shipping_address['first_name'] ?? '' }} {{ $order->shipping_address['last_name'] ?? '' }}<br>
            {{ $order->shipping_address['address1'] ?? '' }}<br>
            {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['country'] ?? '' }}
          </div>
        @endif
      </div>
    </div>

    {{-- Notes --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-2">Note</div>
        <div class="text-secondary small">{{ $order->note ?: 'No notes.' }}</div>
      </div>
    </div>

    {{-- Timeline --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-2">Timeline</div>
        <div class="small">
          <div class="d-flex gap-2 mb-2">
            <i class="bi bi-circle-fill text-primary mt-1" style="font-size:8px;flex-shrink:0;"></i>
            <div><div class="fw-semibold">Order placed</div><div class="text-secondary">{{ $order->created_at->format('M j, Y H:i') }}</div></div>
          </div>
          @if($order->processed_at)
            <div class="d-flex gap-2 mb-2">
              <i class="bi bi-circle-fill text-success mt-1" style="font-size:8px;flex-shrink:0;"></i>
              <div><div class="fw-semibold">Payment confirmed</div><div class="text-secondary">{{ $order->processed_at->format('M j, Y H:i') }}</div></div>
            </div>
          @endif
          @foreach($order->fulfillments as $f)
            <div class="d-flex gap-2 mb-2">
              <i class="bi bi-circle-fill text-info mt-1" style="font-size:8px;flex-shrink:0;"></i>
              <div><div class="fw-semibold">Fulfilled</div><div class="text-secondary">{{ $f->created_at->format('M j, Y H:i') }}</div></div>
            </div>
          @endforeach
          @if($order->cancelled_at)
            <div class="d-flex gap-2 mb-2">
              <i class="bi bi-circle-fill text-danger mt-1" style="font-size:8px;flex-shrink:0;"></i>
              <div><div class="fw-semibold">Cancelled ({{ $order->cancel_reason }})</div><div class="text-secondary">{{ $order->cancelled_at->format('M j, Y H:i') }}</div></div>
            </div>
          @endif
        </div>
      </div>
    </div>

  </div>
</div>

{{-- Fulfill Modal --}}
<div class="modal fade" id="fulfillModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.orders.fulfill', $order) }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Fulfill order</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Tracking company</label>
            <input name="tracking_company" class="form-control" placeholder="DHL, FedEx, TCS…">
          </div>
          <div class="mb-3">
            <label class="form-label">Tracking number</label>
            <input name="tracking_number" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Tracking URL</label>
            <input name="tracking_url" type="url" class="form-control">
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="notify_customer" id="notify_customer" value="1" checked>
            <label class="form-check-label" for="notify_customer">Send shipment notification to customer</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Fulfill order</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Cancel Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.orders.cancel', $order) }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Cancel order</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Reason</label>
            <select name="reason" class="form-select">
              <option value="customer">Customer changed/cancelled order</option>
              <option value="fraud">Fraudulent order</option>
              <option value="inventory">Items unavailable</option>
              <option value="declined">Payment declined</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="restock" value="1" id="restock">
            <label class="form-check-label" for="restock">Restock items</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Back</button>
          <button type="submit" class="btn btn-danger">Cancel order</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Change Status Modal --}}
<div class="modal fade" id="statusModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.orders.status', $order) }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-arrow-repeat me-1"></i> Change order status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-secondary small mb-3">
            Manually override this order's payment and fulfillment status. Customer will be
            notified by email if the toggle below is on.
          </p>

          <div class="mb-3">
            <label class="form-label fw-semibold">Payment status</label>
            <select name="financial_status" class="form-select">
              @php
                $finStates = ['pending','authorized','partially_paid','paid','partially_refunded','refunded','voided'];
              @endphp
              @foreach($finStates as $s)
                <option value="{{ $s }}" @selected($order->financial_status === $s)>
                  {{ ucwords(str_replace('_',' ',$s)) }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Fulfillment status</label>
            <select name="fulfillment_status" class="form-select">
              @php
                $curFul = $order->fulfillment_status ?? 'unfulfilled';
              @endphp
              @foreach(['unfulfilled' => 'Unfulfilled', 'partial' => 'Partial', 'fulfilled' => 'Fulfilled'] as $val => $label)
                <option value="{{ $val }}" @selected($curFul === $val)>{{ $label }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="notifyCustomer"
              name="notify_customer" value="1" checked>
            <label class="form-check-label" for="notifyCustomer">
              Email the customer about this status change
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
