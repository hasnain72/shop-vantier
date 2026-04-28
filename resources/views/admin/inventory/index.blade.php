@extends('admin.layouts.app')
@section('title', 'Inventory')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Inventory</div>
  <a href="{{ route('admin.locations.index') }}" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-geo-alt me-1"></i>Manage locations
  </a>
</div>

{{-- Location tabs --}}
<ul class="nav nav-tabs mb-3">
  <li class="nav-item">
    <a class="nav-link {{ !request('location_id') ? 'active' : '' }}"
      href="{{ route('admin.inventory.index') }}">All locations</a>
  </li>
  @foreach($locations as $loc)
    <li class="nav-item">
      <a class="nav-link {{ request('location_id') == $loc->id ? 'active' : '' }}"
        href="{{ route('admin.inventory.index', ['location_id' => $loc->id]) }}">
        {{ $loc->name }}
      </a>
    </li>
  @endforeach
</ul>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.inventory.index') }}">
  <input type="hidden" name="location_id" value="{{ $activeLocationId }}">
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
      <div class="row g-2 align-items-end">
        <div class="col-md-4">
          <input type="text" name="search" class="form-control form-control-sm"
            placeholder="Search product or SKU…" value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
          <select name="filter" class="form-select form-select-sm">
            <option value="">All stock</option>
            <option value="low_stock"   @selected(request('filter') === 'low_stock')>Low stock (&lt;5)</option>
            <option value="out_of_stock" @selected(request('filter') === 'out_of_stock')>Out of stock</option>
          </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
          <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
          <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.inventory.index', ['location_id' => $activeLocationId]) }}"><i class="bi bi-x-lg"></i></a>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table mb-0 align-middle" id="inventoryTable">
      <thead class="table-light">
        <tr>
          <th style="width:48px;"></th>
          <th>Product</th>
          <th>SKU</th>
          <th>Variant</th>
          <th>Location</th>
          <th class="text-center">Available</th>
          <th class="text-center">Incoming</th>
          <th class="text-center">Committed</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($levels as $level)
          @php
            $variant = $level->inventoryItem?->variant;
            $product = $variant?->product;
          @endphp
          <tr class="{{ $level->available <= 0 ? 'table-danger' : ($level->available < 5 ? 'table-warning' : '') }}">
            <td>
              @if($product?->featured_image)
                <img src="{{ asset('storage/'.$product->featured_image) }}" width="36" height="36"
                  class="rounded" style="object-fit:cover;">
              @else
                <div class="bg-light rounded" style="width:36px;height:36px;"></div>
              @endif
            </td>
            <td>
              <a href="{{ route('admin.products.edit', $product) }}" class="text-decoration-none fw-semibold small">
                {{ $product?->title ?? '—' }}
              </a>
            </td>
            <td class="text-secondary small">{{ $variant?->sku ?? '—' }}</td>
            <td class="text-secondary small">{{ $variant?->title ?? '—' }}</td>
            <td class="text-secondary small">{{ $level->location?->name ?? '—' }}</td>
            <td class="text-center">
              <div class="d-flex align-items-center justify-content-center gap-1">
                <button type="button" class="btn btn-sm btn-outline-secondary px-1 py-0 adjust-btn"
                  data-item-id="{{ $level->inventory_item_id }}"
                  data-location-id="{{ $level->location_id }}"
                  data-adjustment="-1">−</button>
                <span class="fw-semibold adjust-qty" data-item-id="{{ $level->inventory_item_id }}" data-location-id="{{ $level->location_id }}">
                  {{ $level->available }}
                </span>
                <button type="button" class="btn btn-sm btn-outline-secondary px-1 py-0 adjust-btn"
                  data-item-id="{{ $level->inventory_item_id }}"
                  data-location-id="{{ $level->location_id }}"
                  data-adjustment="1">+</button>
              </div>
            </td>
            <td class="text-center text-secondary small">{{ $level->incoming }}</td>
            <td class="text-center text-secondary small">{{ $level->committed }}</td>
            <td>
              <a href="{{ route('admin.inventory.history', $level->inventory_item_id) }}"
                class="btn btn-sm btn-outline-secondary">History</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center text-secondary py-5">No inventory records found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">{{ $levels->withQueryString()->links() }}</div>
@endsection

@push('scripts')
<script>
  document.querySelectorAll('.adjust-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const itemId     = this.dataset.itemId;
      const locationId = this.dataset.locationId;
      const adj        = parseInt(this.dataset.adjustment);

      fetch('{{ route("admin.inventory.adjust") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          inventory_item_id:    itemId,
          location_id:          locationId,
          available_adjustment: adj,
          reason:               'correction',
        }),
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          const span = document.querySelector(`.adjust-qty[data-item-id="${itemId}"][data-location-id="${locationId}"]`);
          if (span) span.textContent = data.available;
        }
      });
    });
  });
</script>
@endpush
