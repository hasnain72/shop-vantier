@extends('admin.layouts.app')

@section('title', 'Products')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <div class="h4 mb-0">Products</div>
    <div class="text-secondary small">{{ $products->total() }} products total</div>
  </div>
  <div class="d-flex gap-2 align-items-center">

    {{-- Image download button --}}
    <div id="imgDownloadWrap">
      <button id="imgDownloadBtn" class="btn btn-outline-secondary btn-sm" onclick="startImageDownload()">
        <i class="fa-solid fa-cloud-arrow-down me-1"></i>
        Download images
        <span id="imgRemaining" class="badge bg-warning text-dark ms-1" style="display:none;"></span>
      </button>
    </div>

    <a class="btn btn-primary" href="{{ route('admin.products.create') }}">
      <i class="bi bi-plus-lg me-1"></i>New product
    </a>
  </div>
</div>

{{-- Download result toast --}}
<div id="imgToast" class="alert alert-info py-2 px-3 mb-3 small d-flex align-items-center gap-2" style="display:none !important;">
  <span id="imgToastMsg"></span>
  <button type="button" class="btn-close btn-close-sm ms-auto" onclick="this.closest('#imgToast').style.display='none'"></button>
</div>

<script>
(function () {
  // Load remaining count on page load
  fetch('{{ route('admin.products.image-download-status') }}')
    .then(r => r.json())
    .then(d => updateBadge(d.remaining));

  function updateBadge(n) {
    var badge = document.getElementById('imgRemaining');
    var btn   = document.getElementById('imgDownloadBtn');
    if (n > 0) {
      badge.textContent = n + ' left';
      badge.style.display = '';
      btn.classList.remove('btn-outline-success');
      btn.classList.add('btn-outline-secondary');
    } else {
      badge.style.display = 'none';
      btn.classList.remove('btn-outline-secondary');
      btn.classList.add('btn-outline-success');
      btn.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> All images local';
      btn.disabled  = true;
    }
  }

  window.startImageDownload = function () {
    var btn = document.getElementById('imgDownloadBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Downloading…';

    fetch('{{ route('admin.products.download-images') }}', {
      method : 'POST',
      headers: {
        'X-CSRF-TOKEN' : '{{ csrf_token() }}',
        'Content-Type' : 'application/json',
        'Accept'       : 'application/json',
      },
      body: JSON.stringify({ batch: 20 }),
    })
    .then(r => r.json())
    .then(function (d) {
      showToast(
        '<i class="fa-solid fa-circle-check text-success me-1"></i>' +
        '<strong>' + d.downloaded + ' images downloaded</strong>' +
        (d.failed ? ', <span class="text-danger">' + d.failed + ' failed</span>' : '') +
        ' — ' + d.remaining + ' products remaining.'
      );
      updateBadge(d.remaining);

      if (!d.done) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-down me-1"></i> Download next 20 <span id="imgRemaining" class="badge bg-warning text-dark ms-1">' + d.remaining + ' left</span>';
      }
    })
    .catch(function (err) {
      showToast('<i class="fa-solid fa-circle-exclamation text-danger me-1"></i> Error: ' + err.message);
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-down me-1"></i> Retry download';
    });
  };

  function showToast(html) {
    var t = document.getElementById('imgToast');
    document.getElementById('imgToastMsg').innerHTML = html;
    t.style.display = 'flex';
  }
})();
</script>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.products.index') }}" id="filterForm">
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
      <div class="row g-2 align-items-end">
        <div class="col-md-3">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Search title or SKU…"
            value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
          <select name="status" class="form-select form-select-sm">
            <option value="">All statuses</option>
            @foreach(['active','draft','archived'] as $s)
              <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <select name="product_type_id" class="form-select form-select-sm">
            <option value="">All types</option>
            @foreach($productTypes as $pt)
              <option value="{{ $pt->id }}" @selected(request('product_type_id') == $pt->id)>{{ $pt->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <select name="vendor" class="form-select form-select-sm">
            <option value="">All vendors</option>
            @foreach($vendors as $v)
              <option value="{{ $v }}" @selected(request('vendor') === $v)>{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <select name="sort" class="form-select form-select-sm">
            <option value="">Newest</option>
            <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
            <option value="title-asc" @selected(request('sort') === 'title-asc')>Title A–Z</option>
            <option value="title-desc" @selected(request('sort') === 'title-desc')>Title Z–A</option>
          </select>
        </div>
        <div class="col-md-1 d-flex gap-1">
          <button class="btn btn-sm btn-primary w-50" type="submit"><i class="bi bi-search"></i></button>
          <a class="btn btn-sm btn-outline-secondary w-50" href="{{ route('admin.products.index') }}"><i class="bi bi-x-lg"></i></a>
        </div>
      </div>
    </div>
  </div>
</form>

{{-- Bulk action form --}}
<form method="POST" action="{{ route('admin.products.index') }}" id="bulkForm">
  @csrf
  @method('PATCH')

  <div class="card border-0 shadow-sm">
    {{-- Bulk toolbar --}}
    <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-2" id="bulkToolbar" style="display:none!important;">
      <span class="text-secondary small me-2" id="bulkCount">0 selected</span>
      <button type="button" class="btn btn-sm btn-outline-success" onclick="bulkAction('publish')">Publish</button>
      <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bulkAction('unpublish')">Unpublish</button>
      <button type="button" class="btn btn-sm btn-outline-warning" onclick="bulkAction('archive')">Archive</button>
      <button type="button" class="btn btn-sm btn-outline-danger" onclick="bulkAction('delete')">Delete</button>
      <input type="hidden" name="bulk_action" id="bulkActionInput">
    </div>

    <div class="table-responsive">
      <table class="table mb-0 align-middle" id="productsTable">
        <thead class="table-light">
          <tr>
            <th style="width:36px;">
              <input type="checkbox" class="form-check-input" id="selectAll">
            </th>
            <th style="width:56px;">Image</th>
            <th>Title</th>
            <th>Status</th>
            <th>Inventory</th>
            <th>Type</th>
            <th>Vendor</th>
            <th>Created</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($products as $product)
            <tr>
              <td>
                <input type="checkbox" class="form-check-input row-check" name="product_ids[]" value="{{ $product->id }}">
              </td>
              <td>
                @if($product->featured_image)
                  <img src="{{ asset('storage/'.$product->featured_image) }}" class="rounded" width="40" height="40" style="object-fit:cover;">
                @else
                  <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                    <i class="bi bi-image text-secondary"></i>
                  </div>
                @endif
              </td>
              <td>
                <a href="{{ route('admin.products.edit', $product) }}" class="fw-semibold text-decoration-none">
                  {{ $product->title }}
                </a>
              </td>
              <td>
                <span class="badge text-bg-{{ $product->status === 'active' ? 'success' : ($product->status === 'draft' ? 'secondary' : 'warning') }}">
                  {{ ucfirst($product->status) }}
                </span>
              </td>
              <td>
                @php $qty = $product->variants->sum('inventory_quantity') @endphp
                <span class="{{ $qty < 5 ? 'text-danger fw-semibold' : 'text-secondary' }}">
                  {{ $qty }} in stock
                </span>
              </td>
              <td class="text-secondary small">{{ $product->productType?->name ?? '—' }}</td>
              <td class="text-secondary small">{{ $product->vendor ?? '—' }}</td>
              <td class="text-secondary small">{{ $product->created_at->format('M j, Y') }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                <button type="button" class="btn btn-sm btn-outline-danger"
                  onclick="confirmDelete('{{ route('admin.products.destroy', $product) }}')">Delete</button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center text-secondary py-5">
                No products found.
                <a href="{{ route('admin.products.create') }}">Create your first product</a>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</form>

<div class="mt-3">
  {{ $products->withQueryString()->links() }}
</div>

{{-- Hidden delete form --}}
<form method="POST" id="deleteForm">
  @csrf
  @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
  // Select all checkboxes
  const selectAll = document.getElementById('selectAll');
  const bulkToolbar = document.getElementById('bulkToolbar');
  const bulkCount = document.getElementById('bulkCount');

  function updateBulkToolbar() {
    const checked = document.querySelectorAll('.row-check:checked');
    if (checked.length > 0) {
      bulkToolbar.style.display = 'flex';
      bulkCount.textContent = checked.length + ' selected';
    } else {
      bulkToolbar.style.removeProperty('display');
    }
  }

  selectAll.addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
    updateBulkToolbar();
  });

  document.querySelectorAll('.row-check').forEach(cb => {
    cb.addEventListener('change', updateBulkToolbar);
  });

  function bulkAction(action) {
    document.getElementById('bulkActionInput').value = action;
    if (action === 'delete') {
      Swal.fire({
        title: 'Delete selected products?',
        text: 'This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, delete',
      }).then(result => { if (result.isConfirmed) document.getElementById('bulkForm').submit(); });
    } else {
      document.getElementById('bulkForm').submit();
    }
  }

  function confirmDelete(url) {
    Swal.fire({
      title: 'Delete this product?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      confirmButtonText: 'Yes, delete',
    }).then(result => {
      if (result.isConfirmed) {
        const f = document.getElementById('deleteForm');
        f.action = url;
        f.submit();
      }
    });
  }
</script>
@endpush
