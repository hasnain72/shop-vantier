@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div class="h4 mb-0">Edit product</div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="{{ route('admin.products.index') }}">Back</a>
      <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger" type="submit">Delete</button>
      </form>
    </div>
  </div>

  <div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
      <form id="productForm" method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.products.form')
        <button class="btn btn-primary" type="submit">Save</button>
      </form>
    </div>
  </div>

  {{-- ===================== ADDONS SECTION ===================== --}}
  <div class="card shadow-sm border-0 mb-3" id="addonsCard">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="fw-semibold">Add-ons <span class="text-secondary fw-normal fs-6">(e.g. Buckle options)</span></div>
        <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addonModal" onclick="openAddonModal()">
          + Add Addon
        </button>
      </div>

      <div class="table-responsive">
        <table class="table mb-0 align-middle" id="addonsTable">
          <thead class="table-light">
            <tr>
              <th style="width:60px">Image</th>
              <th>Name</th>
              <th>SKU</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Policy</th>
              <th>Default</th>
              <th>Active</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody id="addonsTableBody">
            @foreach($product->addons as $addon)
              <tr id="addon-row-{{ $addon->id }}">
                <td>
                  @if($addon->image)
                    <img src="{{ Storage::disk('public')->url($addon->image) }}" width="40" height="40" style="object-fit:cover;border-radius:4px">
                  @else
                    <span class="text-secondary">—</span>
                  @endif
                </td>
                <td>{{ $addon->name }}</td>
                <td class="text-secondary">{{ $addon->sku ?? '—' }}</td>
                <td>${{ number_format((float)$addon->price, 2) }}</td>
                <td>{{ $addon->inventory_quantity }}</td>
                <td><span class="badge {{ $addon->inventory_policy === 'deny' ? 'text-bg-warning' : 'text-bg-info' }}">{{ $addon->inventory_policy }}</span></td>
                <td>
                  @if($addon->is_default)
                    <span class="badge text-bg-success">Yes</span>
                  @else
                    <span class="badge text-bg-secondary">No</span>
                  @endif
                </td>
                <td>
                  @if($addon->is_active)
                    <span class="badge text-bg-success">Yes</span>
                  @else
                    <span class="badge text-bg-secondary">No</span>
                  @endif
                </td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-secondary me-1"
                    onclick='openAddonModal({{ json_encode($addon) }})'>Edit</button>
                  <button class="btn btn-sm btn-outline-info me-1"
                    onclick="openInventoryModal({{ $addon->id }}, {{ $addon->inventory_quantity }}, '{{ addslashes($addon->name) }}')">Stock</button>
                  <button class="btn btn-sm btn-outline-danger"
                    onclick="deleteAddon({{ $addon->id }})">Del</button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Addon Create/Edit Modal --}}
  <div class="modal fade" id="addonModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addonModalTitle">Add Addon</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="addonForm" enctype="multipart/form-data">
          @csrf
          <input type="hidden" id="addonId" value="">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="name" id="addonName" required>
            </div>
            <div class="row g-2 mb-3">
              <div class="col">
                <label class="form-label">SKU</label>
                <input type="text" class="form-control" name="sku" id="addonSku">
              </div>
              <div class="col">
                <label class="form-label">Price ($) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="price" id="addonPrice" min="0" step="0.01" required>
              </div>
            </div>
            <div class="row g-2 mb-3">
              <div class="col">
                <label class="form-label">Inventory Qty <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="inventory_quantity" id="addonQty" min="0" required>
              </div>
              <div class="col">
                <label class="form-label">Inventory Policy</label>
                <select class="form-select" name="inventory_policy" id="addonPolicy">
                  <option value="deny">Deny (out of stock)</option>
                  <option value="continue">Continue selling</option>
                </select>
              </div>
            </div>
            <div class="row g-2 mb-3">
              <div class="col">
                <label class="form-label">Position</label>
                <input type="number" class="form-control" name="position" id="addonPosition" min="0">
              </div>
              <div class="col d-flex align-items-end gap-3 pb-1">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="is_default" id="addonDefault" value="1">
                  <label class="form-check-label" for="addonDefault">Default</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="is_active" id="addonActive" value="1" checked>
                  <label class="form-check-label" for="addonActive">Active</label>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Image</label>
              <input type="file" class="form-control" name="image" id="addonImage" accept="image/*">
              <div id="addonCurrentImage" class="mt-2"></div>
            </div>
            <div id="addonFormError" class="alert alert-danger d-none"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="addonSaveBtn">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Inventory Adjust Modal --}}
  <div class="modal fade" id="inventoryModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Adjust Stock</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="inventoryForm">
          @csrf
          <input type="hidden" id="inventoryAddonId">
          <div class="modal-body">
            <p class="mb-2">Addon: <strong id="inventoryAddonName"></strong></p>
            <p class="mb-3">Current Stock: <strong id="inventoryCurrentQty"></strong></p>
            <div class="mb-3">
              <label class="form-label">Adjustment (+ or -)</label>
              <input type="number" class="form-control" id="inventoryAdjustment" name="adjustment" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Reason</label>
              <select class="form-select" id="inventoryReason" name="reason">
                <option value="received">Received</option>
                <option value="correction">Correction</option>
                <option value="return">Return</option>
                <option value="damaged">Damaged</option>
                <option value="theft">Theft</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div id="inventoryError" class="alert alert-danger d-none"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
    const productId = {{ $product->id }};
    const addonStoreUrl  = '{{ route('admin.products.addons.store', $product) }}';
    const addonBaseUrl   = '{{ route('admin.products.addons.store', $product) }}';

    function openAddonModal(addon = null) {
      document.getElementById('addonId').value       = addon ? addon.id : '';
      document.getElementById('addonName').value     = addon ? addon.name : '';
      document.getElementById('addonSku').value      = addon ? (addon.sku || '') : '';
      document.getElementById('addonPrice').value    = addon ? addon.price : '0.00';
      document.getElementById('addonQty').value      = addon ? addon.inventory_quantity : '0';
      document.getElementById('addonPolicy').value   = addon ? addon.inventory_policy : 'deny';
      document.getElementById('addonPosition').value = addon ? addon.position : '';
      document.getElementById('addonDefault').checked = addon ? !!addon.is_default : false;
      document.getElementById('addonActive').checked  = addon ? !!addon.is_active  : true;
      document.getElementById('addonFormError').classList.add('d-none');
      document.getElementById('addonModalTitle').textContent = addon ? 'Edit Addon' : 'Add Addon';

      const imgDiv = document.getElementById('addonCurrentImage');
      imgDiv.innerHTML = addon && addon.image_url
        ? `<img src="${addon.image_url}" height="50" style="border-radius:4px"> <small class="text-secondary ms-2">Current image (upload new to replace)</small>`
        : '';

      new bootstrap.Modal(document.getElementById('addonModal')).show();
    }

    document.getElementById('addonForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const addonId = document.getElementById('addonId').value;
      const url = addonId
        ? `{{ url('admin/products/' . $product->id . '/addons') }}/${addonId}`
        : addonStoreUrl;
      const method = addonId ? 'PUT' : 'POST';

      const fd = new FormData(this);
      if (addonId) fd.append('_method', 'PUT');

      document.getElementById('addonSaveBtn').disabled = true;
      document.getElementById('addonFormError').classList.add('d-none');

      try {
        const res = await fetch(url, { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || fd.get('_token') } });
        const json = await res.json();
        if (!res.ok) throw new Error(json.message || JSON.stringify(json.errors || {}));
        location.reload();
      } catch (err) {
        const errDiv = document.getElementById('addonFormError');
        errDiv.textContent = err.message;
        errDiv.classList.remove('d-none');
      } finally {
        document.getElementById('addonSaveBtn').disabled = false;
      }
    });

    async function deleteAddon(addonId) {
      if (!confirm('Delete this addon?')) return;
      const res = await fetch(`{{ url('admin/products/' . $product->id . '/addons') }}/${addonId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `_method=DELETE&_token={{ csrf_token() }}`
      });
      if (res.ok) {
        document.getElementById(`addon-row-${addonId}`)?.remove();
      } else {
        alert('Failed to delete addon.');
      }
    }

    function openInventoryModal(addonId, currentQty, name) {
      document.getElementById('inventoryAddonId').value = addonId;
      document.getElementById('inventoryAddonName').textContent = name;
      document.getElementById('inventoryCurrentQty').textContent = currentQty;
      document.getElementById('inventoryAdjustment').value = '';
      document.getElementById('inventoryError').classList.add('d-none');
      new bootstrap.Modal(document.getElementById('inventoryModal')).show();
    }

    document.getElementById('inventoryForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const addonId = document.getElementById('inventoryAddonId').value;
      const fd = new FormData(this);

      try {
        const res = await fetch(`{{ url('admin/products/' . $product->id . '/addons') }}/${addonId}/adjust-inventory`, {
          method: 'POST',
          body: fd,
          headers: { 'X-CSRF-TOKEN': fd.get('_token') }
        });
        const json = await res.json();
        if (!res.ok) throw new Error(json.message || 'Error');
        location.reload();
      } catch (err) {
        const errDiv = document.getElementById('inventoryError');
        errDiv.textContent = err.message;
        errDiv.classList.remove('d-none');
      }
    });

    // ── Variant Bulk Price ─────────────────────────────────────
    const variantBulkPriceUrl = '{{ route('admin.products.variants.bulk-price-update', $product) }}';

    function getSelectedVariantIds() {
      return Array.from(document.querySelectorAll('.variant-check:checked')).map(cb => cb.value);
    }

    function updateVariantBulkBtn() {
      const ids = getSelectedVariantIds();
      const btn = document.getElementById('variantBulkPriceBtn');
      document.getElementById('variantSelectedCount').textContent = ids.length;
      ids.length > 0 ? btn.classList.remove('d-none') : btn.classList.add('d-none');
    }

    document.getElementById('variantSelectAll').addEventListener('change', function () {
      document.querySelectorAll('.variant-check').forEach(cb => cb.checked = this.checked);
      updateVariantBulkBtn();
    });

    document.querySelectorAll('.variant-check').forEach(cb => {
      cb.addEventListener('change', updateVariantBulkBtn);
    });

    const vbpAction = document.getElementById('vbpAction');
    const vbpPrefix = document.getElementById('vbpPrefix');
    const vbpValue  = document.getElementById('vbpValue');

    vbpAction.addEventListener('change', function () {
      const isPct = this.value.includes('percent');
      vbpPrefix.textContent   = isPct ? '%' : '$';
      vbpValue.step           = isPct ? '1' : '0.01';
      vbpValue.placeholder    = isPct ? '0' : '0.00';
      updateVbpPreview();
    });
    vbpValue.addEventListener('input', updateVbpPreview);

    document.getElementById('variantBulkPriceModal').addEventListener('show.bs.modal', function () {
      const ids = getSelectedVariantIds();
      document.getElementById('vbpSelectedLabel').textContent =
        ids.length ? ids.length : 'all';
      document.getElementById('vbpError').classList.add('d-none');
      document.getElementById('vbpSuccess').classList.add('d-none');
      document.getElementById('vbpPreview').classList.add('d-none');
    });

    function updateVbpPreview() {
      const val  = parseFloat(vbpValue.value);
      const prev = document.getElementById('vbpPreview');
      if (isNaN(val) || val < 0) { prev.classList.add('d-none'); return; }

      const labels = {
        set_fixed:        `All selected → $${val.toFixed(2)}`,
        increase_fixed:   `Current price + $${val.toFixed(2)}`,
        decrease_fixed:   `Current price − $${val.toFixed(2)}`,
        increase_percent: `Current price × ${(1 + val/100).toFixed(4)} (+${val}%)`,
        decrease_percent: `Current price × ${(1 - val/100).toFixed(4)} (−${val}%)`,
      };
      prev.textContent = labels[vbpAction.value] || '';
      prev.classList.remove('d-none');
    }

    async function applyVariantBulkPrice() {
      const val = parseFloat(vbpValue.value);
      if (isNaN(val) || val < 0) { alert('Enter a valid value.'); return; }

      const ids    = getSelectedVariantIds();
      const errDiv = document.getElementById('vbpError');
      const sucDiv = document.getElementById('vbpSuccess');
      const btn    = document.getElementById('vbpApplyBtn');

      errDiv.classList.add('d-none');
      sucDiv.classList.add('d-none');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Applying…';

      try {
        const res = await fetch(variantBulkPriceUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            variant_ids:  ids.length ? ids : null,
            price_action: vbpAction.value,
            price_value:  val,
          }),
        });

        const json = await res.json();
        if (!res.ok) throw new Error(json.message || 'Something went wrong.');

        sucDiv.textContent = json.message;
        sucDiv.classList.remove('d-none');

        // Update prices in table without page reload
        (json.variants || []).forEach(v => {
          const el = document.getElementById(`vprice-${v.id}`);
          if (el) el.textContent = '$' + parseFloat(v.price).toFixed(2);
        });

        // Uncheck all after apply
        document.querySelectorAll('.variant-check').forEach(cb => cb.checked = false);
        document.getElementById('variantSelectAll').checked = false;
        updateVariantBulkBtn();

      } catch (err) {
        errDiv.textContent = err.message;
        errDiv.classList.remove('d-none');
      } finally {
        btn.disabled = false;
        btn.textContent = 'Apply';
      }
    }
  </script>
  @endpush

  {{-- ===================== VARIANTS SECTION ===================== --}}
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="fw-semibold">Variants</div>
        <div class="d-flex gap-2">
          <button class="btn btn-sm btn-outline-primary d-none" id="variantBulkPriceBtn"
            data-bs-toggle="modal" data-bs-target="#variantBulkPriceModal">
            <i class="bi bi-tag me-1"></i>Update Price
            <span id="variantSelectedCount" class="badge bg-primary ms-1">0</span>
          </button>
          <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.products.variants.index', $product) }}">Manage variants</a>
          <a class="btn btn-sm btn-primary" href="{{ route('admin.products.variants.create', $product) }}">Add variant</a>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:36px"><input type="checkbox" class="form-check-input" id="variantSelectAll"></th>
              <th>Title</th>
              <th>SKU</th>
              <th>Price</th>
              <th>Qty</th>
              <th>Active</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($product->variants as $variant)
              <tr id="vrow-{{ $variant->id }}">
                <td>
                  <input type="checkbox" class="form-check-input variant-check" value="{{ $variant->id }}">
                </td>
                <td>{{ $variant->title }}</td>
                <td class="text-secondary">{{ $variant->sku }}</td>
                <td><span class="variant-price" id="vprice-{{ $variant->id }}">${{ number_format((float)$variant->price, 2) }}</span></td>
                <td>{{ $variant->inventory_quantity }}</td>
                <td>
                  @if($variant->is_active)
                    <span class="badge text-bg-success">Yes</span>
                  @else
                    <span class="badge text-bg-secondary">No</span>
                  @endif
                </td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.products.variants.edit', [$product, $variant]) }}">Edit</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Variant Bulk Price Modal --}}
  <div class="modal fade" id="variantBulkPriceModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-tag me-1"></i> Bulk Price Update</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-secondary small mb-3">
            Updating <strong id="vbpSelectedLabel">all</strong> variant(s) of this product.
          </p>

          <div class="mb-3">
            <label class="form-label fw-semibold">Action</label>
            <select class="form-select" id="vbpAction">
              <option value="set_fixed">Set fixed price</option>
              <option value="increase_fixed">Increase by fixed amount ($)</option>
              <option value="decrease_fixed">Decrease by fixed amount ($)</option>
              <option value="increase_percent">Increase by percentage (%)</option>
              <option value="decrease_percent">Decrease by percentage (%)</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Value</label>
            <div class="input-group">
              <span class="input-group-text" id="vbpPrefix">$</span>
              <input type="number" class="form-control" id="vbpValue" min="0" step="0.01" placeholder="0.00">
            </div>
          </div>

          <div id="vbpPreview" class="alert alert-secondary py-2 small d-none"></div>
          <div id="vbpError"   class="alert alert-danger  py-2 small d-none"></div>
          <div id="vbpSuccess" class="alert alert-success py-2 small d-none"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="vbpApplyBtn" onclick="applyVariantBulkPrice()">Apply</button>
        </div>
      </div>
    </div>
  </div>
@endsection

