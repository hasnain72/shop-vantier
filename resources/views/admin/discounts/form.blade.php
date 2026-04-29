@csrf
<div class="row g-3">
  {{-- LEFT ----------------------------------------------------------------- --}}
  <div class="col-lg-8">

    {{-- Section 1: Codes --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">Discount code(s)</div>
        <div id="codesWrapper">
          @foreach(old('codes', isset($discount) ? $discount->discountCodes->pluck('code')->toArray() : ['']) as $i => $code)
            <div class="input-group mb-2 code-row">
              <input type="text" name="codes[]" class="form-control text-uppercase"
                value="{{ strtoupper($code) }}" placeholder="e.g. SUMMER20" required>
              <button type="button" class="btn btn-outline-secondary" onclick="generateCode(this)">Generate</button>
              <button type="button" class="btn btn-outline-danger" onclick="removeCode(this)">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          @endforeach
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCodeRow()">
          <i class="bi bi-plus-lg me-1"></i>Add another code
        </button>
      </div>
    </div>

    {{-- Section 2: Type & Value --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">Discount type</div>
        <div class="mb-3">
          @foreach(['percentage' => 'Percentage', 'fixed_amount' => 'Fixed amount', 'free_shipping' => 'Free shipping', 'buy_x_get_y' => 'Buy X Get Y'] as $val => $label)
            <div class="form-check">
              <input class="form-check-input" type="radio" name="value_type" id="vt_{{ $val }}"
                value="{{ $val }}" onchange="onValueTypeChange()"
                {{ old('value_type', $discount->value_type ?? 'percentage') === $val ? 'checked' : '' }}>
              <label class="form-check-label" for="vt_{{ $val }}">{{ $label }}</label>
            </div>
          @endforeach
        </div>
        <div id="valueRow">
          <label class="form-label" id="valueLabel">Discount value</label>
          <div class="input-group" style="max-width:200px;">
            <span class="input-group-text" id="valuePrefix">%</span>
            <input type="number" name="value" id="valueInput" step="0.01" min="0"
              class="form-control @error('value') is-invalid @enderror"
              value="{{ old('value', isset($discount) ? abs($discount->value) : '') }}">
          </div>
          @error('value')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>

    {{-- Section 3: Applies To --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">Applies to</div>
        <div class="mb-3">
          @foreach(['all' => 'All products', 'entitled' => 'Specific products / collections'] as $val => $label)
            <div class="form-check">
              <input class="form-check-input" type="radio" name="target_selection" id="ts_{{ $val }}"
                value="{{ $val }}" onchange="onTargetChange()"
                {{ old('target_selection', $discount->target_selection ?? 'all') === $val ? 'checked' : '' }}>
              <label class="form-check-label" for="ts_{{ $val }}">{{ $label }}</label>
            </div>
          @endforeach
        </div>

        <div id="entitledSection" class="{{ old('target_selection', $discount->target_selection ?? 'all') === 'all' ? 'd-none' : '' }}">
          <label class="form-label small fw-semibold">Collections</label>
          <div class="border rounded p-2 mb-2" style="max-height:140px;overflow-y:auto;">
            @foreach($collections as $col)
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="entitled_collection_ids[]"
                  value="{{ $col->id }}" id="col_{{ $col->id }}"
                  {{ in_array($col->id, old('entitled_collection_ids', $discount->entitled_collection_ids ?? [])) ? 'checked' : '' }}>
                <label class="form-check-label small" for="col_{{ $col->id }}">{{ $col->title }}</label>
              </div>
            @endforeach
          </div>
          <label class="form-label small fw-semibold mt-1">Products</label>
          <input type="text" id="productSearch" class="form-control form-control-sm mb-1" placeholder="Search products…" oninput="filterProducts()">
          <div class="border rounded p-2" style="max-height:160px;overflow-y:auto;" id="productList">
            @foreach($products as $prod)
              <div class="form-check product-item" data-title="{{ strtolower($prod->title) }}">
                <input class="form-check-input" type="checkbox" name="entitled_product_ids[]"
                  value="{{ $prod->id }}" id="prod_{{ $prod->id }}"
                  {{ in_array($prod->id, old('entitled_product_ids', $discount->entitled_product_ids ?? [])) ? 'checked' : '' }}>
                <label class="form-check-label small" for="prod_{{ $prod->id }}">{{ $prod->title }}</label>
              </div>
            @endforeach
          </div>
        </div>

        <hr>
        <div class="fw-semibold mb-2">Minimum requirements</div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="min_req" id="min_none" value="none"
            onchange="onMinReqChange()" {{ !old('min_purchase_amount') && !old('min_quantity') ? 'checked' : '' }}>
          <label class="form-check-label" for="min_none">None</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="min_req" id="min_amount" value="amount"
            onchange="onMinReqChange()" {{ old('min_purchase_amount') ? 'checked' : '' }}>
          <label class="form-check-label" for="min_amount">Minimum purchase amount ($)</label>
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="radio" name="min_req" id="min_qty" value="qty"
            onchange="onMinReqChange()" {{ old('min_quantity') ? 'checked' : '' }}>
          <label class="form-check-label" for="min_qty">Minimum quantity of items</label>
        </div>
        <div id="minAmountRow" class="d-none mb-2" style="max-width:180px;">
          <input type="number" name="min_purchase_amount" step="0.01" min="0" class="form-control"
            placeholder="0.00" value="{{ old('min_purchase_amount', isset($discount) ? ($discount->prerequisite_subtotal_range['greater_than_or_equal_to'] ?? '') : '') }}">
        </div>
        <div id="minQtyRow" class="d-none mb-2" style="max-width:120px;">
          <input type="number" name="min_quantity" min="1" class="form-control"
            placeholder="1" value="{{ old('min_quantity', isset($discount) ? ($discount->prerequisite_quantity_range['greater_than_or_equal_to'] ?? '') : '') }}">
        </div>
      </div>
    </div>

    {{-- Section 4: Customer eligibility --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">Customer eligibility</div>
        @foreach(['all' => 'All customers', 'prerequisite' => 'Specific customers'] as $val => $label)
          <div class="form-check">
            <input class="form-check-input" type="radio" name="customer_selection"
              id="cs_{{ $val }}" value="{{ $val }}"
              {{ old('customer_selection', $discount->customer_selection ?? 'all') === $val ? 'checked' : '' }}>
            <label class="form-check-label" for="cs_{{ $val }}">{{ $label }}</label>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Section 5: Usage limits --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">Usage limits</div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="limitToggle" value="1"
            onchange="document.getElementById('usageLimitInput').closest('.mt-1').classList.toggle('d-none', !this.checked)"
            {{ old('usage_limit', $discount->usage_limit ?? null) ? 'checked' : '' }}>
          <label class="form-check-label" for="limitToggle">Limit number of times this discount can be used in total</label>
        </div>
        <div class="mt-1 {{ old('usage_limit') ? '' : 'd-none' }}" style="max-width:140px;">
          <input type="number" name="usage_limit" id="usageLimitInput" min="1" class="form-control"
            value="{{ old('usage_limit', $discount->usage_limit ?? '') }}" placeholder="e.g. 100">
        </div>
        <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox" name="once_per_customer" id="oncePerCustomer" value="1"
            {{ old('once_per_customer', $discount->once_per_customer ?? false) ? 'checked' : '' }}>
          <label class="form-check-label" for="oncePerCustomer">Limit to one use per customer</label>
        </div>
      </div>
    </div>

  </div>

  {{-- RIGHT ---------------------------------------------------------------- --}}
  <div class="col-lg-4">

    {{-- Active dates --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">Active dates</div>
        <div class="mb-3">
          <label class="form-label small">Start date <span class="text-danger">*</span></label>
          <input type="text" name="starts_at" class="form-control flatpickr @error('starts_at') is-invalid @enderror"
            value="{{ old('starts_at', isset($discount) ? $discount->starts_at?->format('Y-m-d H:i') : now()->format('Y-m-d H:i')) }}"
            placeholder="YYYY-MM-DD HH:MM">
          @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="form-label small">End date <span class="text-secondary">(optional)</span></label>
          <input type="text" name="ends_at" class="form-control flatpickr"
            value="{{ old('ends_at', isset($discount) ? $discount->ends_at?->format('Y-m-d H:i') : '') }}"
            placeholder="YYYY-MM-DD HH:MM">
        </div>
      </div>
    </div>

    {{-- Summary --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-2">Summary</div>
        <div class="mb-2">
          <label class="form-label small">Title / Internal name <span class="text-danger">*</span></label>
          <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', $discount->title ?? '') }}" placeholder="e.g. Summer Sale 2025" required>
          @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>

  </div>
</div>

@push('scripts')
<script>
function onValueTypeChange() {
  const type   = document.querySelector('input[name="value_type"]:checked')?.value;
  const row    = document.getElementById('valueRow');
  const prefix = document.getElementById('valuePrefix');
  if (type === 'free_shipping' || type === 'buy_x_get_y') {
    row.classList.add('d-none');
  } else {
    row.classList.remove('d-none');
    prefix.textContent = type === 'percentage' ? '%' : '$';
  }
}

function onTargetChange() {
  const val = document.querySelector('input[name="target_selection"]:checked')?.value;
  document.getElementById('entitledSection').classList.toggle('d-none', val === 'all');
}

function onMinReqChange() {
  const val = document.querySelector('input[name="min_req"]:checked')?.value;
  document.getElementById('minAmountRow').classList.toggle('d-none', val !== 'amount');
  document.getElementById('minQtyRow').classList.toggle('d-none', val !== 'qty');
}

function addCodeRow() {
  const wrapper = document.getElementById('codesWrapper');
  const div = document.createElement('div');
  div.className = 'input-group mb-2 code-row';
  div.innerHTML = `<input type="text" name="codes[]" class="form-control text-uppercase" placeholder="e.g. SAVE10" required>
    <button type="button" class="btn btn-outline-secondary" onclick="generateCode(this)">Generate</button>
    <button type="button" class="btn btn-outline-danger" onclick="removeCode(this)"><i class="bi bi-trash"></i></button>`;
  wrapper.appendChild(div);
}

function removeCode(btn) {
  const rows = document.querySelectorAll('.code-row');
  if (rows.length > 1) btn.closest('.code-row').remove();
}

async function generateCode(btn) {
  const res  = await fetch('{{ route("admin.discounts.generate-code") }}');
  const data = await res.json();
  btn.closest('.code-row').querySelector('input').value = data.code;
}

function filterProducts() {
  const q = document.getElementById('productSearch').value.toLowerCase();
  document.querySelectorAll('.product-item').forEach(el => {
    el.classList.toggle('d-none', !el.dataset.title.includes(q));
  });
}

// Init flatpickr on date inputs
document.querySelectorAll('.flatpickr').forEach(el => {
  if (window.flatpickr) flatpickr(el, { enableTime: true, dateFormat: 'Y-m-d H:i' });
});

// Init state on load
onValueTypeChange();
onMinReqChange();
</script>
@endpush
