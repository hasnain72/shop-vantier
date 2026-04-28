@extends('admin.layouts.app')
@section('title', 'Shipping')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Shipping zones</div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#zoneModal" onclick="openZoneModal()">
    <i class="bi bi-plus-lg me-1"></i>Add shipping zone
  </button>
</div>

@if($zones->isEmpty())
  <div class="card border-0 shadow-sm">
    <div class="card-body text-center text-secondary py-5">
      No shipping zones configured. Add a zone to start shipping.
    </div>
  </div>
@else
  <div class="d-flex flex-column gap-3">
    @foreach($zones as $zone)
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3">
        <div>
          <span class="fw-semibold">{{ $zone->name }}</span>
          <div class="mt-1 d-flex flex-wrap gap-1">
            @foreach($zone->countries ?? [] as $cc)
              <span class="badge text-bg-secondary">{{ strtoupper($cc) }}</span>
            @endforeach
          </div>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-sm btn-outline-secondary"
            onclick="openZoneModal({{ $zone->id }}, '{{ addslashes($zone->name) }}', {{ json_encode($zone->countries ?? []) }})">
            Edit
          </button>
          <form method="POST" action="{{ route('admin.shipping.zones.delete', $zone) }}"
            onsubmit="return confirm('Delete zone and all its rates?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Delete</button>
          </form>
        </div>
      </div>
      <div class="card-body p-0">
        @if($zone->rates->isEmpty())
          <div class="text-secondary small px-4 py-3">No rates yet.</div>
        @else
          <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
              <thead class="table-light">
                <tr>
                  <th class="ps-4">Rate name</th>
                  <th>Type</th>
                  <th>Price</th>
                  <th>Condition</th>
                  <th>Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach($zone->rates as $rate)
                <tr>
                  <td class="ps-4">{{ $rate->name }}</td>
                  <td>
                    <span class="badge text-bg-light border">
                      {{ match($rate->rate_type) {
                        'flat'         => 'Flat rate',
                        'free'         => 'Free shipping',
                        'price_based'  => 'Price-based',
                        'weight_based' => 'Weight-based',
                        default        => $rate->rate_type,
                      } }}
                    </span>
                  </td>
                  <td>{{ $rate->rate_type === 'free' ? 'Free' : '$'.number_format($rate->price,2) }}</td>
                  <td class="text-secondary small">
                    @if($rate->rate_type === 'price_based')
                      @if($rate->min_order_subtotal !== null && $rate->max_order_subtotal !== null)
                        ${{ number_format($rate->min_order_subtotal,2) }} – ${{ number_format($rate->max_order_subtotal,2) }}
                      @elseif($rate->min_order_subtotal !== null)
                        Over ${{ number_format($rate->min_order_subtotal,2) }}
                      @elseif($rate->max_order_subtotal !== null)
                        Under ${{ number_format($rate->max_order_subtotal,2) }}
                      @endif
                    @elseif($rate->rate_type === 'weight_based')
                      @if($rate->min_weight !== null && $rate->max_weight !== null)
                        {{ $rate->min_weight }} – {{ $rate->max_weight }} kg
                      @elseif($rate->min_weight !== null)
                        Over {{ $rate->min_weight }} kg
                      @elseif($rate->max_weight !== null)
                        Under {{ $rate->max_weight }} kg
                      @endif
                    @else
                      —
                    @endif
                  </td>
                  <td>
                    @if($rate->is_active)
                      <span class="badge text-bg-success">Active</span>
                    @else
                      <span class="badge text-bg-secondary">Inactive</span>
                    @endif
                  </td>
                  <td class="pe-3">
                    <div class="d-flex gap-1 justify-content-end">
                      <button class="btn btn-sm btn-outline-secondary"
                        onclick="openRateModal({{ $zone->id }}, {{ $rate->id }}, {{ $rate->toJson() }})">
                        Edit
                      </button>
                      <form method="POST" action="{{ route('admin.shipping.rates.delete', $rate) }}"
                        onsubmit="return confirm('Delete this rate?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                      </form>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
        <div class="px-4 py-3 border-top">
          <button class="btn btn-sm btn-outline-primary"
            onclick="openRateModal({{ $zone->id }})">
            <i class="bi bi-plus-lg me-1"></i>Add rate
          </button>
        </div>
      </div>
    </div>
    @endforeach
  </div>
@endif

{{-- Zone Modal --}}
<div class="modal fade" id="zoneModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="zoneForm" method="POST">
      @csrf
      <input type="hidden" name="_method" id="zoneMethod" value="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="zoneModalTitle">Add shipping zone</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Zone name <span class="text-danger">*</span></label>
            <input name="name" id="zoneName" class="form-control" required placeholder="e.g. Domestic, Rest of World">
          </div>
          <div class="mb-3">
            <label class="form-label">Countries (ISO 2-letter codes) <span class="text-danger">*</span></label>
            <textarea name="countries_raw" id="zoneCountries" class="form-control" rows="3"
              placeholder="PK, US, GB, AE (comma-separated)"></textarea>
            <div class="form-text">Enter comma-separated 2-letter country codes.</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="zoneSubmitBtn">Create zone</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Rate Modal --}}
<div class="modal fade" id="rateModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="rateForm" method="POST">
      @csrf
      <input type="hidden" name="_method" id="rateMethod" value="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="rateModalTitle">Add shipping rate</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Rate name <span class="text-danger">*</span></label>
            <input name="name" id="rateName" class="form-control" required placeholder="e.g. Standard, Express">
          </div>
          <div class="mb-3">
            <label class="form-label">Rate type <span class="text-danger">*</span></label>
            <select name="rate_type" id="rateType" class="form-select" required onchange="onRateTypeChange()">
              <option value="flat">Flat rate</option>
              <option value="free">Free shipping</option>
              <option value="price_based">Price-based</option>
              <option value="weight_based">Weight-based</option>
            </select>
          </div>
          <div class="mb-3" id="priceRow">
            <label class="form-label">Price ($)</label>
            <input name="price" id="ratePrice" type="number" step="0.01" min="0" class="form-control" value="0">
          </div>
          <div id="priceCondition" class="d-none">
            <div class="row g-3 mb-3">
              <div class="col-6">
                <label class="form-label">Min order amount</label>
                <input name="min_order_subtotal" id="minOrderSubtotal" type="number" step="0.01" min="0" class="form-control" placeholder="No minimum">
              </div>
              <div class="col-6">
                <label class="form-label">Max order amount</label>
                <input name="max_order_subtotal" id="maxOrderSubtotal" type="number" step="0.01" min="0" class="form-control" placeholder="No maximum">
              </div>
            </div>
          </div>
          <div id="weightCondition" class="d-none">
            <div class="row g-3 mb-3">
              <div class="col-6">
                <label class="form-label">Min weight (kg)</label>
                <input name="min_weight" id="minWeight" type="number" step="0.001" min="0" class="form-control" placeholder="No minimum">
              </div>
              <div class="col-6">
                <label class="form-label">Max weight (kg)</label>
                <input name="max_weight" id="maxWeight" type="number" step="0.001" min="0" class="form-control" placeholder="No maximum">
              </div>
            </div>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" id="rateActive" value="1" checked>
            <label class="form-check-label" for="rateActive">Active</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="rateSubmitBtn">Add rate</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function openZoneModal(id, name, countries) {
  const form  = document.getElementById('zoneForm');
  const title = document.getElementById('zoneModalTitle');
  const btn   = document.getElementById('zoneSubmitBtn');

  if (id) {
    form.action = `/admin/shipping/zones/${id}`;
    document.getElementById('zoneMethod').value = 'PUT';
    title.textContent = 'Edit shipping zone';
    btn.textContent   = 'Save changes';
    document.getElementById('zoneName').value = name;
    document.getElementById('zoneCountries').value = (countries || []).join(', ');
  } else {
    form.action = '{{ route("admin.shipping.zones.store") }}';
    document.getElementById('zoneMethod').value = 'POST';
    title.textContent = 'Add shipping zone';
    btn.textContent   = 'Create zone';
    document.getElementById('zoneName').value = '';
    document.getElementById('zoneCountries').value = '';
  }
}

function openRateModal(zoneId, rateId, rate) {
  const form  = document.getElementById('rateForm');
  const title = document.getElementById('rateModalTitle');
  const btn   = document.getElementById('rateSubmitBtn');

  if (rateId && rate) {
    form.action = `/admin/shipping/rates/${rateId}`;
    document.getElementById('rateMethod').value = 'PUT';
    title.textContent = 'Edit shipping rate';
    btn.textContent   = 'Save changes';
    document.getElementById('rateName').value    = rate.name;
    document.getElementById('rateType').value    = rate.rate_type;
    document.getElementById('ratePrice').value   = rate.price;
    document.getElementById('rateActive').checked = !!rate.is_active;
    document.getElementById('minOrderSubtotal').value = rate.min_order_subtotal ?? '';
    document.getElementById('maxOrderSubtotal').value = rate.max_order_subtotal ?? '';
    document.getElementById('minWeight').value        = rate.min_weight ?? '';
    document.getElementById('maxWeight').value        = rate.max_weight ?? '';
  } else {
    form.action = `/admin/shipping/zones/${zoneId}/rates`;
    document.getElementById('rateMethod').value = 'POST';
    title.textContent = 'Add shipping rate';
    btn.textContent   = 'Add rate';
    document.getElementById('rateName').value   = '';
    document.getElementById('rateType').value   = 'flat';
    document.getElementById('ratePrice').value  = '0';
    document.getElementById('rateActive').checked = true;
    document.getElementById('minOrderSubtotal').value = '';
    document.getElementById('maxOrderSubtotal').value = '';
    document.getElementById('minWeight').value        = '';
    document.getElementById('maxWeight').value        = '';
  }

  onRateTypeChange();
  new bootstrap.Modal(document.getElementById('rateModal')).show();
}

function onRateTypeChange() {
  const type   = document.getElementById('rateType').value;
  const priceRow      = document.getElementById('priceRow');
  const priceCondition = document.getElementById('priceCondition');
  const weightCondition = document.getElementById('weightCondition');

  priceRow.classList.toggle('d-none', type === 'free');
  priceCondition.classList.toggle('d-none', type !== 'price_based');
  weightCondition.classList.toggle('d-none', type !== 'weight_based');
}

// Parse countries textarea into array inputs on zone form submit
document.getElementById('zoneForm').addEventListener('submit', function (e) {
  const raw = document.getElementById('zoneCountries').value;
  const codes = raw.split(/[\s,]+/).map(c => c.trim().toUpperCase()).filter(Boolean);

  // Remove any existing country[] inputs
  this.querySelectorAll('input[name="countries[]"]').forEach(el => el.remove());

  codes.forEach(code => {
    const input = document.createElement('input');
    input.type  = 'hidden';
    input.name  = 'countries[]';
    input.value = code;
    this.appendChild(input);
  });
});
</script>
@endpush
