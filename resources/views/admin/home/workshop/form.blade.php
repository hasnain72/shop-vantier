@extends('admin.layouts.app')
@section('title', $item ? 'Edit Workshop Item' : 'New Workshop Item')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Home Page</div>
</div>

{{-- Tab nav --}}
<ul class="nav nav-tabs mb-4">
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.settings') }}">Settings</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.craft-cards') }}">Craft Cards</a></li>
  <li class="nav-item"><a class="nav-link active" href="{{ route('admin.home.workshop') }}">Workshop Items</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.reviews') }}">Reviews</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.faqs') }}">FAQs</a></li>
</ul>

<form method="POST"
      action="{{ $item
        ? route('admin.home.workshop.update', $item)
        : route('admin.home.workshop.store') }}">
  @csrf
  @if($item) @method('PUT') @endif

  @if($errors->any())
    <div class="alert alert-danger mb-3">
      <ul class="mb-0 ps-3">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <div class="row g-4">

    {{-- ── Left column ──────────────────────────────────────────────── --}}
    <div class="col-lg-8 d-flex flex-column gap-4">

      {{-- Titles --}}
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Titles</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Title (English) <span class="text-danger">*</span></label>
              <input name="title_en" class="form-control @error('title_en') is-invalid @enderror"
                     value="{{ old('title_en', $item->title_en ?? '') }}" required>
              @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Title (Arabic) <span class="text-danger">*</span></label>
              <input name="title_ar" dir="rtl"
                     class="form-control @error('title_ar') is-invalid @enderror"
                     value="{{ old('title_ar', $item->title_ar ?? '') }}" required>
              @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
      </div>

      {{-- Media --}}
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Media</div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Video MP4 URL <span class="text-danger">*</span></label>
            <input name="video_mp4" type="url"
                   class="form-control @error('video_mp4') is-invalid @enderror"
                   value="{{ old('video_mp4', $item->video_mp4 ?? '') }}"
                   placeholder="https://...mp4" required>
            @error('video_mp4')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Video Poster URL <span class="text-muted fw-normal">(optional)</span></label>
            <input name="video_poster" type="url"
                   class="form-control @error('video_poster') is-invalid @enderror"
                   value="{{ old('video_poster', $item->video_poster ?? '') }}"
                   placeholder="https://...jpg">
            @error('video_poster')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Thumbnail URL <span class="text-danger">*</span></label>
            <input name="thumb_url" type="url"
                   class="form-control @error('thumb_url') is-invalid @enderror"
                   value="{{ old('thumb_url', $item->thumb_url ?? '') }}"
                   placeholder="https://...jpg" required>
            @error('thumb_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-0">
            <label class="form-label fw-semibold">Modal Gallery Image URLs
              <span class="text-muted fw-normal">(one per line)</span>
            </label>
            <textarea name="modal_gallery_urls" rows="4" class="form-control"
                      placeholder="https://...jpg&#10;https://...jpg">{{ old('modal_gallery_urls', isset($item) ? implode("\n", $item->modal_gallery_urls ?? []) : '') }}</textarea>
          </div>
        </div>
      </div>

      {{-- Modal Body --}}
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Modal Body HTML
          <span class="text-muted fw-normal small">(optional)</span>
        </div>
        <div class="card-body">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Primary HTML (English)</label>
              <textarea name="modal_primary_en" rows="4" class="form-control"
                        placeholder="<p>...</p>">{{ old('modal_primary_en', $item->modal_body_primary_html['en'] ?? '') }}</textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Primary HTML (Arabic)</label>
              <textarea name="modal_primary_ar" rows="4" dir="rtl" class="form-control"
                        placeholder="<p>...</p>">{{ old('modal_primary_ar', $item->modal_body_primary_html['ar'] ?? '') }}</textarea>
            </div>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Secondary HTML (English)</label>
              <textarea name="modal_secondary_en" rows="4" class="form-control"
                        placeholder="<p>...</p>">{{ old('modal_secondary_en', $item->modal_body_secondary_html['en'] ?? '') }}</textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Secondary HTML (Arabic)</label>
              <textarea name="modal_secondary_ar" rows="4" dir="rtl" class="form-control"
                        placeholder="<p>...</p>">{{ old('modal_secondary_ar', $item->modal_body_secondary_html['ar'] ?? '') }}</textarea>
            </div>
          </div>
        </div>
      </div>

      {{-- Size Options --}}
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
          <span class="fw-semibold">Size Options</span>
          <button type="button" class="btn btn-sm btn-outline-primary" id="addSizeBtn">
            <i class="fa-solid fa-plus me-1"></i> Add Size
          </button>
        </div>
        <div class="card-body p-0">
          <table class="table table-sm mb-0" id="sizesTable">
            <thead class="table-light">
              <tr>
                <th>ID / Key</th>
                <th>Label (EN)</th>
                <th>Label (AR)</th>
                <th>Variant</th>
                <th style="width:44px;"></th>
              </tr>
            </thead>
            <tbody id="sizeRows">
              @php
                $sizeOptions = old('size_ids')
                  ? array_map(fn($id, $len, $lar, $lv) => ['id'=>$id,'label'=>['en'=>$len,'ar'=>$lar],'variant'=>$lv],
                      old('size_ids',[]), old('size_labels_en',[]), old('size_labels_ar',[]), old('size_variants',[]))
                  : ($item->size_options ?? []);
              @endphp
              @foreach($sizeOptions as $s)
                <tr class="size-row">
                  <td><input name="size_ids[]" class="form-control form-control-sm" value="{{ $s['id'] }}"></td>
                  <td><input name="size_labels_en[]" class="form-control form-control-sm" value="{{ $s['label']['en'] ?? '' }}"></td>
                  <td><input name="size_labels_ar[]" dir="rtl" class="form-control form-control-sm" value="{{ $s['label']['ar'] ?? '' }}"></td>
                  <td>
                    <select name="size_variants[]" class="form-select form-select-sm">
                      <option value="">None</option>
                      <option value="apple" {{ ($s['variant'] ?? '') === 'apple' ? 'selected' : '' }}>apple</option>
                    </select>
                  </td>
                  <td>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-size-btn">
                      <i class="fa-solid fa-xmark"></i>
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
          @if(empty($sizeOptions))
            <p class="text-secondary small text-center py-3 mb-0" id="noSizesMsg">No size options. Click "Add Size" to add one.</p>
          @endif
        </div>
      </div>

    </div>{{-- /left col --}}

    {{-- ── Right column ─────────────────────────────────────────────── --}}
    <div class="col-lg-4 d-flex flex-column gap-4">

      {{-- Visibility --}}
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Visibility</div>
        <div class="card-body">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActiveSwitch"
                   {{ old('is_active', $item->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="isActiveSwitch">Show on home page</label>
          </div>
        </div>
      </div>

      {{-- Pricing --}}
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Pricing</div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Currency <span class="text-danger">*</span></label>
            <input name="currency" class="form-control @error('currency') is-invalid @enderror"
                   value="{{ old('currency', $item->currency ?? 'USD') }}" required>
            @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Current Price <span class="text-danger">*</span></label>
            <input name="current_price" class="form-control @error('current_price') is-invalid @enderror"
                   value="{{ old('current_price', $item->current_price ?? '') }}" required>
            @error('current_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-0">
            <label class="form-label fw-semibold">Original Price <span class="text-muted fw-normal">(optional)</span></label>
            <input name="original_price" class="form-control @error('original_price') is-invalid @enderror"
                   value="{{ old('original_price', $item->original_price ?? '') }}">
            @error('original_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>

      {{-- Product path & sort --}}
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Link & Order</div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Product Path <span class="text-muted fw-normal">(optional)</span></label>
            <input name="product_path" class="form-control @error('product_path') is-invalid @enderror"
                   value="{{ old('product_path', $item->product_path ?? '') }}"
                   placeholder="/products/watch-roll">
            @error('product_path')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-0">
            <label class="form-label fw-semibold">Sort Order</label>
            <input name="sort_order" type="number" min="0"
                   class="form-control @error('sort_order') is-invalid @enderror"
                   value="{{ old('sort_order', $item->sort_order ?? 0) }}">
            @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>

      {{-- Actions --}}
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-grow-1">
          {{ $item ? 'Save Changes' : 'Create Item' }}
        </button>
        <a href="{{ route('admin.home.workshop') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </div>{{-- /right col --}}
  </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
  const tbody    = document.getElementById('sizeRows');
  const noMsg    = document.getElementById('noSizesMsg');
  const addBtn   = document.getElementById('addSizeBtn');

  function refreshNoMsg() {
    if (!noMsg) return;
    noMsg.style.display = tbody.querySelectorAll('.size-row').length ? 'none' : '';
  }
  refreshNoMsg();

  function makeRow(id, labelEn, labelAr, variant) {
    const tr = document.createElement('tr');
    tr.className = 'size-row';
    tr.innerHTML = `
      <td><input name="size_ids[]" class="form-control form-control-sm" value="${id}"></td>
      <td><input name="size_labels_en[]" class="form-control form-control-sm" value="${labelEn}"></td>
      <td><input name="size_labels_ar[]" dir="rtl" class="form-control form-control-sm" value="${labelAr}"></td>
      <td>
        <select name="size_variants[]" class="form-select form-select-sm">
          <option value="">None</option>
          <option value="apple" ${variant === 'apple' ? 'selected' : ''}>apple</option>
        </select>
      </td>
      <td>
        <button type="button" class="btn btn-sm btn-outline-danger remove-size-btn">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </td>`;
    tr.querySelector('.remove-size-btn').addEventListener('click', function () {
      tr.remove();
      refreshNoMsg();
    });
    return tr;
  }

  // Attach remove listeners to pre-filled rows
  tbody.querySelectorAll('.remove-size-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      btn.closest('tr').remove();
      refreshNoMsg();
    });
  });

  addBtn.addEventListener('click', function () {
    tbody.appendChild(makeRow('', '', '', ''));
    refreshNoMsg();
  });
})();
</script>
@endpush
