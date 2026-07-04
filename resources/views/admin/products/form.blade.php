@csrf

<div class="row g-4">
  {{-- ========== LEFT COLUMN (8) ========== --}}
  <div class="col-lg-8">

    {{-- Title & Description with EN / AR tabs --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">

        {{-- Language tabs --}}
        <ul class="nav nav-tabs mb-3" id="productLangTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="prod-en-tab" data-bs-toggle="tab"
              data-bs-target="#prod-en" type="button" role="tab">🇬🇧 English</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="prod-ar-tab" data-bs-toggle="tab"
              data-bs-target="#prod-ar" type="button" role="tab">
              🇸🇦 Arabic
              @php $arFilled = !empty(old('title_ar', $product?->title_ar)); @endphp
              @if($arFilled)
                <span class="badge text-bg-success ms-1" style="font-size:0.65rem;">✓</span>
              @else
                <span class="badge text-bg-secondary ms-1" style="font-size:0.65rem;">empty</span>
              @endif
            </button>
          </li>
        </ul>

        <div class="tab-content" id="productLangTabContent">

          {{-- English tab --}}
          <div class="tab-pane fade show active" id="prod-en" role="tabpanel">
            <div class="mb-3">
              <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
              <input name="title" class="form-control @error('title') is-invalid @enderror"
                value="{{ old('title', $product?->title ?? '') }}" placeholder="e.g. Amethyst Gator" required>
              @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-0">
              <label class="form-label fw-semibold">Description</label>
              <textarea name="body_html" id="body_html"
                class="form-control @error('body_html') is-invalid @enderror"
                rows="7">{{ old('body_html', $product?->body_html ?? '') }}</textarea>
              @error('body_html')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Arabic tab --}}
          <div class="tab-pane fade" id="prod-ar" role="tabpanel" dir="rtl">
            <div class="mb-3">
              <label class="form-label fw-semibold">العنوان بالعربية</label>
              <input name="title_ar" class="form-control text-end @error('title_ar') is-invalid @enderror"
                value="{{ old('title_ar', $product?->title_ar ?? '') }}"
                placeholder="مثال: أمثيست غايتور" dir="rtl">
              @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-0">
              <label class="form-label fw-semibold">الوصف بالعربية</label>
              <textarea name="body_html_ar" id="body_html_ar"
                class="form-control text-end @error('body_html_ar') is-invalid @enderror"
                rows="7" dir="rtl">{{ old('body_html_ar', $product?->body_html_ar ?? '') }}</textarea>
              @error('body_html_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @php
              $arBody = old('body_html_ar', $product?->body_html_ar ?? '');
              $arTitle = old('title_ar', $product?->title_ar ?? '');
              $arCount = (int)!empty($arTitle) + (int)!empty($arBody);
            @endphp
            <div class="mt-2">
              <div class="d-flex justify-content-between mb-1">
                <small class="text-muted">Arabic translation</small>
                <small class="text-muted">{{ $arCount }}/2 fields</small>
              </div>
              <div class="progress" style="height:4px;">
                <div class="progress-bar bg-success" style="width:{{ $arCount * 50 }}%"></div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    {{-- Media --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <label class="form-label fw-semibold">Media</label>

        {{-- Existing images --}}
        @if(isset($product) && $product->relationLoaded('productImages') && $product->productImages->isNotEmpty())
          <div class="row g-2 mb-3" id="existingImages">
            @foreach($product->productImages->sortBy('position') as $img)
              <div class="col-3 col-md-2 position-relative" data-image-id="{{ $img->id }}">
                <img src="{{ asset('storage/'.$img->src) }}" class="img-thumbnail" style="width:100%;height:80px;object-fit:cover;">
                <input type="hidden" name="keep_image_ids[]" value="{{ $img->id }}">
                <button type="button"
                  class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0 lh-1"
                  style="width:18px;height:18px;font-size:10px;"
                  onclick="removeExistingImage(this, {{ $img->id }})">×</button>
              </div>
            @endforeach
          </div>
          <div id="deletedImageInputs"></div>
        @endif

        {{-- Dropzone --}}
        <div id="imageDropzone" class="dropzone border rounded p-3 text-center text-secondary"
          style="min-height:120px;cursor:pointer;">
          <div class="dz-message">
            <i class="bi bi-cloud-upload fs-3 d-block mb-1"></i>
            <span>Drop images here or click to upload</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Pricing --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">Pricing</div>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Price <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input name="default_price" type="number" step="0.01" min="0"
                class="form-control @error('default_price') is-invalid @enderror"
                value="{{ old('default_price', isset($product) ? optional($product->variants->first())->price : '0.00') }}"
                @if(!isset($product)) required @endif>
              @error('default_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Compare-at price</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input name="compare_at_price" type="number" step="0.01" min="0"
                class="form-control"
                value="{{ old('compare_at_price', isset($product) ? optional($product->variants->first())->compare_at_price : '') }}">
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Cost per item</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input name="cost_per_item" type="number" step="0.01" min="0"
                class="form-control"
                value="{{ old('cost_per_item', isset($product) ? optional($product->variants->first())->cost_per_item : '') }}">
            </div>
          </div>
        </div>
        <div class="form-check mt-3">
          <input class="form-check-input" type="checkbox" value="1" id="taxable" name="taxable"
            @checked(old('taxable', $product?->taxable ?? true))>
          <label class="form-check-label" for="taxable">Charge tax on this product</label>
        </div>
      </div>
    </div>

    {{-- Inventory --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">Inventory</div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">SKU (Stock Keeping Unit)</label>
            <input name="default_sku" class="form-control @error('default_sku') is-invalid @enderror"
              value="{{ old('default_sku', isset($product) ? optional($product->variants->first())->sku : '') }}">
            @error('default_sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Barcode (ISBN, UPC, GTIN, etc.)</label>
            <input name="barcode" class="form-control"
              value="{{ old('barcode', isset($product) ? optional($product->variants->first())->barcode : '') }}">
          </div>
        </div>
        <div class="row g-3 mt-1">
          <div class="col-md-6">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="track_quantity" name="track_quantity"
                @checked(old('track_quantity', true))>
              <label class="form-check-label" for="track_quantity">Track quantity</label>
            </div>
          </div>
        </div>
        <div class="mt-3" id="quantitySection">
          <label class="form-label">Quantity</label>
          <input name="inventory_quantity" type="number" min="0" class="form-control"
            style="max-width:160px;"
            value="{{ old('inventory_quantity', isset($product) ? optional($product->variants->first())->inventory_quantity : '0') }}">
        </div>
      </div>
    </div>

    {{-- Shipping --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">Shipping</div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" value="1" id="requires_shipping" name="requires_shipping"
            @checked(old('requires_shipping', $product?->requires_shipping ?? true))>
          <label class="form-check-label" for="requires_shipping">This is a physical product</label>
        </div>
        <div id="shippingFields">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Weight</label>
              <div class="input-group">
                <input name="weight" type="number" step="0.001" min="0" class="form-control"
                  value="{{ old('weight', isset($product) ? optional($product->variants->first())->weight : '') }}">
                <select name="weight_unit" class="form-select" style="max-width:80px;">
                  @foreach(['kg','g','lb','oz'] as $unit)
                    <option value="{{ $unit }}" @selected(old('weight_unit', optional($product?->variants?->first())?->weight_unit ?? 'kg') === $unit)>{{ $unit }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Country / Region of origin</label>
              <input name="country_of_origin" class="form-control"
                value="{{ old('country_of_origin') }}">
            </div>
          </div>
          <div class="mt-3">
            <label class="form-label">Harmonized System (HS) code</label>
            <input name="hs_code" class="form-control" style="max-width:260px;"
              value="{{ old('hs_code') }}">
          </div>
        </div>
      </div>
    </div>

    {{-- Variants --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">Options / Variants</div>
        <div id="optionsList"></div>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="addOptionBtn">
          <i class="bi bi-plus-lg me-1"></i>Add option (e.g. Size, Color)
        </button>
        <div class="mt-3" id="variantMatrix" style="display:none;">
          <div class="fw-semibold small text-secondary mb-2">Variant preview</div>
          <div id="variantPreview" class="row g-2"></div>
        </div>
      </div>
    </div>

  </div>{{-- /left col --}}

  {{-- ========== RIGHT COLUMN (4) ========== --}}
  <div class="col-lg-4">

    {{-- Status --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
          @foreach(['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived'] as $val => $label)
            <option value="{{ $val }}" @selected(old('status', $product?->status ?? 'draft') === $val)>{{ $label }}</option>
          @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror

        <label class="form-label fw-semibold mt-3">Published at</label>
        <input type="text" name="published_at" id="published_at" class="form-control flatpickr"
          placeholder="Schedule publish date…"
          value="{{ old('published_at', optional($product?->published_at)?->format('Y-m-d H:i')) }}">
      </div>
    </div>

    {{-- Collections --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <label class="form-label fw-semibold">Collections</label>
        @php($selectedCols = old('collection_ids', isset($product) ? $product->collections->pluck('id')->all() : []))
        <div style="max-height:200px;overflow-y:auto;">
          @foreach($collections as $col)
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="collection_ids[]"
                value="{{ $col->id }}" id="col_{{ $col->id }}"
                @checked(in_array($col->id, $selectedCols))>
              <label class="form-check-label" for="col_{{ $col->id }}">
                {{ $col->title }}
                @if(!empty($col->title_ar))
                  <span class="text-secondary" dir="rtl" style="font-size:0.85em;">({{ $col->title_ar }})</span>
                @endif
              </label>
            </div>
          @endforeach
          @if($collections->isEmpty())
            <div class="text-secondary small">No collections yet.</div>
          @endif
        </div>
      </div>
    </div>

    {{-- Product type & Vendor --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <label class="form-label fw-semibold">Product type</label>
        <select name="product_type_id" class="form-select @error('product_type_id') is-invalid @enderror">
          <option value="">—</option>
          @foreach($productTypes as $pt)
            <option value="{{ $pt->id }}" @selected(old('product_type_id', $product?->product_type_id ?? null) == $pt->id)>{{ $pt->name }}</option>
          @endforeach
        </select>
        @error('product_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror

        <label class="form-label fw-semibold mt-3">Vendor</label>
        <input name="vendor" class="form-control @error('vendor') is-invalid @enderror"
          value="{{ old('vendor', $product?->vendor ?? '') }}" placeholder="e.g. Nike">
        @error('vendor')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>

    {{-- Product Category --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <label class="form-label fw-semibold">Product Category</label>
        <select name="product_category" class="form-select @error('product_category') is-invalid @enderror">
          <option value="">— None —</option>
          @foreach(\App\Models\ProductCategory::orderBy('sort_position')->orderBy('name')->get() as $cat)
            <option value="{{ $cat->slug }}"
              @selected(old('product_category', $product?->product_category ?? '') === $cat->slug)>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
        @error('product_category')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">
          <a href="{{ route('admin.product-categories.index') }}" target="_blank">Manage categories</a>
        </div>
      </div>
    </div>

    {{-- Tags --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <label class="form-label fw-semibold">Tags</label>
        <select name="tags[]" id="tagsInput" class="form-select" multiple>
          @php($existingTags = old('tags', isset($product) ? ($product->tags ?? []) : []))
          @foreach($existingTags as $tag)
            <option value="{{ $tag }}" selected>{{ $tag }}</option>
          @endforeach
        </select>
        <div class="form-text">Type and press Enter to add tags.</div>
      </div>
    </div>

    {{-- SEO --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">Search engine listing</div>
        <label class="form-label">Meta title</label>
        <input name="meta_title" class="form-control mb-2" value="{{ old('meta_title', $product?->meta_title ?? '') }}">
        <label class="form-label">Meta description</label>
        <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $product?->meta_description ?? '') }}</textarea>
      </div>
    </div>

  </div>{{-- /right col --}}
</div>{{-- /row --}}

@push('scripts')
<script>
  // ── Flatpickr
  flatpickr('#published_at', { enableTime: true, dateFormat: 'Y-m-d H:i' });

  // ── Select2 Tags
  $('#tagsInput').select2({
    tags: true,
    tokenSeparators: [',', ' '],
    placeholder: 'Add tags…',
  });

  // ── Dropzone
  Dropzone.autoDiscover = false;
  const dz = new Dropzone('#imageDropzone', {
    url: '#',
    autoProcessQueue: false,
    addRemoveLinks: true,
    acceptedFiles: 'image/*',
    paramName: 'images[]',
    init: function () {
      this.on('addedfile', function () {});
    }
  });

  // Intercept form submit to attach dropzone files
  document.getElementById('productForm').addEventListener('submit', function (e) {
    if (dz.files.length > 0) {
      e.preventDefault();
      const form = this;
      form.enctype = 'multipart/form-data';
      const dt = new DataTransfer();
      dz.files.forEach(f => dt.items.add(f));
      let input = form.querySelector('input[name="images[]"]');
      if (!input) {
        input = document.createElement('input');
        input.type = 'file';
        input.name = 'images[]';
        input.multiple = true;
        input.style.display = 'none';
        form.appendChild(input);
      }
      input.files = dt.files;
      form.submit();
    } else {
      this.enctype = 'multipart/form-data';
    }
  });

  // ── Remove existing image
  function removeExistingImage(btn, id) {
    const card = btn.closest('[data-image-id]');
    card.remove();
    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = 'delete_image_ids[]';
    hidden.value = id;
    document.getElementById('deletedImageInputs').appendChild(hidden);
  }

  // ── Shipping toggle
  document.getElementById('requires_shipping').addEventListener('change', function () {
    document.getElementById('shippingFields').style.display = this.checked ? '' : 'none';
  });

  // ── Track quantity toggle
  document.getElementById('track_quantity').addEventListener('change', function () {
    document.getElementById('quantitySection').style.display = this.checked ? '' : 'none';
  });

  // ── Variants / Options builder
  let optionCount = 0;
  const optionNames = ['Size', 'Color', 'Material'];

  document.getElementById('addOptionBtn').addEventListener('click', addOption);

  function addOption() {
    if (optionCount >= 3) {
      alert('Maximum 3 options allowed.');
      return;
    }
    const idx = optionCount++;
    const suggestedName = optionNames[idx] || 'Option ' + (idx + 1);
    const html = `
      <div class="card border mb-2 option-item" data-idx="${idx}">
        <div class="card-body p-2">
          <div class="d-flex gap-2 align-items-start">
            <div class="flex-grow-1">
              <input type="text" name="options[${idx}][name]" class="form-control form-control-sm mb-1 option-name"
                placeholder="Option name" value="${suggestedName}">
              <input type="text" name="options[${idx}][values]" class="form-control form-control-sm option-values"
                placeholder="Comma-separated values (S, M, L)">
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeOption(this)">×</button>
          </div>
        </div>
      </div>`;
    document.getElementById('optionsList').insertAdjacentHTML('beforeend', html);

    // Re-bind change listeners
    document.querySelectorAll('.option-name, .option-values').forEach(el => {
      el.removeEventListener('input', updateVariantPreview);
      el.addEventListener('input', updateVariantPreview);
    });

    if (optionCount >= 3) document.getElementById('addOptionBtn').style.display = 'none';
    updateVariantPreview();
  }

  function removeOption(btn) {
    btn.closest('.option-item').remove();
    optionCount--;
    document.getElementById('addOptionBtn').style.display = '';
    updateVariantPreview();
  }

  function updateVariantPreview() {
    const items = document.querySelectorAll('.option-item');
    const options = [];
    items.forEach(item => {
      const name = item.querySelector('.option-name').value.trim();
      const values = item.querySelector('.option-values').value
        .split(',').map(v => v.trim()).filter(v => v);
      if (name && values.length) options.push({ name, values });
    });

    if (!options.length) {
      document.getElementById('variantMatrix').style.display = 'none';
      return;
    }

    // Cartesian product
    let combinations = [[]];
    options.forEach(opt => {
      const newC = [];
      combinations.forEach(existing => {
        opt.values.forEach(val => newC.push([...existing, val]));
      });
      combinations = newC;
    });

    document.getElementById('variantMatrix').style.display = '';
    const preview = document.getElementById('variantPreview');
    preview.innerHTML = combinations.map((combo, i) => `
      <div class="col-12 col-md-6">
        <div class="border rounded p-2 small d-flex align-items-center gap-2">
          <span class="flex-grow-1">${combo.join(' / ')}</span>
          <input type="number" step="0.01" min="0" placeholder="Price"
            name="variant_prices[${i}]" class="form-control form-control-sm" style="width:90px;">
          <input type="text" placeholder="SKU"
            name="variant_skus[${i}]" class="form-control form-control-sm" style="width:90px;">
          <input type="hidden" name="variant_combos[${i}]" value="${combo.join(' / ')}">
        </div>
      </div>`).join('');
  }
</script>
@endpush
