@extends('admin.layouts.app')

@section('title', 'Product Categories')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <div class="h4 mb-0">Product Categories</div>
    <div class="text-secondary small">Tools, Watch Roll, Buckle, etc. — shown as category cards on the storefront</div>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="openModal()">
    <i class="bi bi-plus-lg me-1"></i>Add Category
  </button>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show py-2">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead class="table-light">
        <tr>
          <th style="width:70px">Image</th>
          <th>Name</th>
          <th>Slug</th>
          <th>Products</th>
          <th>Sort</th>
          <th>Active</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($categories as $cat)
          <tr>
            <td>
              @if($cat->image)
                <img src="{{ Storage::disk('public')->url($cat->image) }}" width="50" height="50" style="object-fit:cover;border-radius:6px;">
              @else
                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                  <i class="bi bi-image text-secondary"></i>
                </div>
              @endif
            </td>
            <td class="fw-semibold">{{ $cat->name }}</td>
            <td><code class="text-secondary">{{ $cat->slug }}</code></td>
            <td>
              <span class="badge text-bg-secondary">
                {{ \App\Models\Product::where('product_category', $cat->slug)->where('status','active')->count() }}
              </span>
            </td>
            <td class="text-secondary">{{ $cat->sort_position }}</td>
            <td>
              @if($cat->is_active)
                <span class="badge text-bg-success">Active</span>
              @else
                <span class="badge text-bg-secondary">Hidden</span>
              @endif
            </td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-secondary"
                onclick='openModal({{ json_encode($cat) }})'>Edit</button>
              <form method="POST" action="{{ route('admin.product-categories.destroy', $cat) }}"
                class="d-inline" onsubmit="return confirm('Delete this category?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Del</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-secondary py-5">
              No categories yet. Add one to get started.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Create / Edit Modal --}}
<div class="modal fade" id="categoryModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="categoryModalTitle">Add Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="categoryForm" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_method" id="categoryMethod" value="POST">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="catName" class="form-control" required
              placeholder="e.g. Tools, Watch Roll, Buckle & Clasp">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Slug</label>
            <input type="text" name="slug" id="catSlug" class="form-control"
              placeholder="auto-generated from name">
            <div class="form-text">Used in API & URLs. Leave blank to auto-generate.</div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Description</label>
            <textarea name="description" id="catDesc" class="form-control" rows="2"
              placeholder="Short description for this category"></textarea>
          </div>
          <div class="row g-3 mb-3">
            <div class="col">
              <label class="form-label fw-semibold">Sort Position</label>
              <input type="number" name="sort_position" id="catSort" class="form-control" min="0" value="0">
            </div>
            <div class="col d-flex align-items-end pb-1">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="catActive" value="1" checked>
                <label class="form-check-label" for="catActive">Active (show on storefront)</label>
              </div>
            </div>
          </div>
          <div class="mb-2">
            <label class="form-label fw-semibold">Category Image</label>
            <input type="file" name="image" id="catImage" class="form-control" accept="image/*">
            <div id="catCurrentImage" class="mt-2"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Auto-slug from name
  document.getElementById('catName').addEventListener('input', function () {
    const slugField = document.getElementById('catSlug');
    if (!slugField.dataset.manual) {
      slugField.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    }
  });
  document.getElementById('catSlug').addEventListener('input', function () {
    this.dataset.manual = this.value ? '1' : '';
  });

  function openModal(cat = null) {
    const form   = document.getElementById('categoryForm');
    const title  = document.getElementById('categoryModalTitle');
    const method = document.getElementById('categoryMethod');

    if (cat) {
      title.textContent  = 'Edit Category';
      method.value       = 'PUT';
      form.action        = `/admin/product-categories/${cat.id}`;
      document.getElementById('catName').value   = cat.name;
      document.getElementById('catSlug').value   = cat.slug;
      document.getElementById('catDesc').value   = cat.description || '';
      document.getElementById('catSort').value   = cat.sort_position;
      document.getElementById('catActive').checked = !!cat.is_active;

      const imgDiv = document.getElementById('catCurrentImage');
      imgDiv.innerHTML = cat.image_url
        ? `<img src="${cat.image_url}" height="55" style="border-radius:6px;"> <small class="text-secondary ms-2">Upload new to replace</small>`
        : '';
    } else {
      title.textContent  = 'Add Category';
      method.value       = 'POST';
      form.action        = '{{ route('admin.product-categories.store') }}';
      form.reset();
      document.getElementById('catActive').checked = true;
      document.getElementById('catCurrentImage').innerHTML = '';
      delete document.getElementById('catSlug').dataset.manual;
    }

    new bootstrap.Modal(document.getElementById('categoryModal')).show();
  }
</script>
@endpush
