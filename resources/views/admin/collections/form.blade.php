@csrf

<div class="row g-4">
  {{-- ===== LEFT (8) ===== --}}
  <div class="col-lg-8">

    {{-- Title --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
        <input name="title" class="form-control @error('title') is-invalid @enderror"
          value="{{ old('title', $collection->title ?? '') }}" placeholder="Summer Sale" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>

    {{-- Description --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <label class="form-label fw-semibold">Description</label>
        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="5">{{ old('description', $collection->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>

    {{-- Image upload --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <label class="form-label fw-semibold">Collection Image</label>

        @if(isset($collection) && $collection->image)
          <div class="mb-2" id="currentImageWrap">
            <img src="{{ asset('storage/'.$collection->image) }}" class="img-thumbnail" style="max-height:160px;">
            <div class="mt-1">
              <div class="form-check">
                <input type="checkbox" class="form-check-input" name="remove_image" id="remove_image" value="1">
                <label class="form-check-label text-danger" for="remove_image">Remove image</label>
              </div>
            </div>
          </div>
        @endif

        <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">
        <div id="imagePreviewWrap" class="mt-2" style="display:none;">
          <img id="imagePreview" class="img-thumbnail" style="max-height:160px;">
        </div>
      </div>
    </div>

    {{-- Products assignment --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-2">Products in this collection</div>
        <input type="text" id="productSearch" class="form-control form-control-sm mb-2"
          placeholder="Search products…">
        <div style="max-height:280px;overflow-y:auto;" id="productList">
          @php($selectedProducts = old('product_ids', isset($collection) ? $collection->products->pluck('id')->all() : []))
          @foreach($products as $p)
            <div class="form-check border-bottom py-1 product-item"
              data-title="{{ strtolower($p->title) }}">
              <input class="form-check-input" type="checkbox" name="product_ids[]"
                value="{{ $p->id }}" id="prod_{{ $p->id }}"
                @checked(in_array($p->id, $selectedProducts))>
              <label class="form-check-label d-flex align-items-center gap-2" for="prod_{{ $p->id }}">
                @if($p->featured_image)
                  <img src="{{ asset('storage/'.$p->featured_image) }}" width="28" height="28"
                    class="rounded" style="object-fit:cover;">
                @else
                  <div class="bg-light rounded" style="width:28px;height:28px;"></div>
                @endif
                {{ $p->title }}
              </label>
            </div>
          @endforeach
          @if($products->isEmpty())
            <div class="text-secondary small py-2">No products yet.</div>
          @endif
        </div>
      </div>
    </div>

    {{-- SEO --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">Search engine listing</div>
        <label class="form-label">Meta title</label>
        <input name="meta_title" class="form-control mb-2"
          value="{{ old('meta_title', $collection->meta_title ?? '') }}">
        <label class="form-label">Meta description</label>
        <textarea name="meta_description" class="form-control" rows="2">{{ old('meta_description', $collection->meta_description ?? '') }}</textarea>
      </div>
    </div>

  </div>{{-- /left --}}

  {{-- ===== RIGHT (4) ===== --}}
  <div class="col-lg-4">

    {{-- Visibility --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-2">Visibility</div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="1" id="published" name="published"
            @checked(old('published', $collection->published ?? true))>
          <label class="form-check-label" for="published">Published (visible on storefront)</label>
        </div>
        <div class="mt-2">
          <label class="form-label small">Published at</label>
          <input type="text" name="published_at" class="form-control form-control-sm flatpickr"
            placeholder="Schedule…"
            value="{{ old('published_at', optional($collection->published_at ?? null)?->format('Y-m-d H:i')) }}">
        </div>
      </div>
    </div>

    {{-- Sort order --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <label class="form-label fw-semibold">Sort products by</label>
        @php($sortValue = old('sort_order', $collection->sort_order ?? 'manual'))
        <select name="sort_order" class="form-select @error('sort_order') is-invalid @enderror">
          @foreach([
            'manual'       => 'Manually',
            'best-selling' => 'Best selling',
            'alpha-asc'    => 'Alphabetically: A–Z',
            'alpha-desc'   => 'Alphabetically: Z–A',
            'price-asc'    => 'Price: Low to high',
            'price-desc'   => 'Price: High to low',
            'created-asc'  => 'Date: Old to new',
            'created-desc' => 'Date: New to old',
          ] as $val => $label)
            <option value="{{ $val }}" @selected($sortValue === $val)>{{ $label }}</option>
          @endforeach
        </select>
        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>

    {{-- Template suffix --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <label class="form-label fw-semibold">Theme template</label>
        <div class="input-group">
          <span class="input-group-text text-secondary">collection.</span>
          <input name="template_suffix" class="form-control"
            value="{{ old('template_suffix', $collection->template_suffix ?? '') }}"
            placeholder="default">
        </div>
      </div>
    </div>

  </div>{{-- /right --}}
</div>

@push('scripts')
<script>
  flatpickr('.flatpickr', { enableTime: true, dateFormat: 'Y-m-d H:i' });

  // Image preview
  document.getElementById('imageInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('imagePreview').src = e.target.result;
      document.getElementById('imagePreviewWrap').style.display = '';
    };
    reader.readAsDataURL(file);
  });

  // Product search filter
  document.getElementById('productSearch').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.product-item').forEach(item => {
      item.style.display = item.dataset.title.includes(q) ? '' : 'none';
    });
  });
</script>
@endpush
