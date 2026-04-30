@extends('admin.layouts.app')
@section('title', 'Product Types')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <div class="h4 mb-0">Product Types</div>
    <div class="text-secondary small">{{ $productTypes->count() }} type{{ $productTypes->count() !== 1 ? 's' : '' }} total</div>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTypeModal">
    <i class="bi bi-plus-lg me-1"></i>Add type
  </button>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead class="table-light">
        <tr>
          <th>Name</th>
          <th>Slug</th>
          <th>Description</th>
          <th>Products</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($productTypes as $type)
          <tr>
            <td class="fw-semibold">{{ $type->name }}</td>
            <td class="text-secondary small">{{ $type->slug }}</td>
            <td class="text-secondary small">{{ Str::limit($type->description, 60) ?: '–' }}</td>
            <td>
              <span class="badge text-bg-secondary">{{ $type->products_count }}</span>
            </td>
            <td>
              @if($type->is_active)
                <span class="badge text-bg-success">Active</span>
              @else
                <span class="badge text-bg-secondary">Inactive</span>
              @endif
            </td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-secondary"
                data-bs-toggle="modal" data-bs-target="#editTypeModal{{ $type->id }}">Edit</button>
              <form method="POST" action="{{ route('admin.product-types.destroy', $type) }}"
                class="d-inline"
                onsubmit="return confirm('Delete product type \'{{ $type->name }}\'?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"
                  {{ $type->products_count > 0 ? 'disabled title=Products assigned' : '' }}>Delete</button>
              </form>
            </td>
          </tr>

          {{-- Edit modal --}}
          <div class="modal fade" id="editTypeModal{{ $type->id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <form method="POST" action="{{ route('admin.product-types.update', $type) }}">
                  @csrf @method('PUT')
                  <div class="modal-header">
                    <h5 class="modal-title">Edit product type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label small fw-semibold">Name <span class="text-danger">*</span></label>
                      <input type="text" name="name" class="form-control"
                        value="{{ old('name', $type->name) }}" required maxlength="100">
                    </div>
                    <div class="mb-3">
                      <label class="form-label small fw-semibold">Description</label>
                      <textarea name="description" class="form-control" rows="2"
                        maxlength="500">{{ old('description', $type->description) }}</textarea>
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" name="is_active" id="is_active_{{ $type->id }}"
                        value="1" {{ $type->is_active ? 'checked' : '' }}>
                      <label class="form-check-label small" for="is_active_{{ $type->id }}">Active</label>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @empty
          <tr>
            <td colspan="6" class="text-center text-secondary py-5">
              No product types yet.<br>
              <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addTypeModal">Add your first type</button>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Add type modal --}}
<div class="modal fade" id="addTypeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.product-types.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add product type</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control"
              placeholder="e.g. Watch Straps" required maxlength="100" value="{{ old('name') }}">
            @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Description</label>
            <textarea name="description" class="form-control" rows="2"
              maxlength="500" placeholder="Optional description">{{ old('description') }}</textarea>
          </div>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active_new" value="1" checked>
            <label class="form-check-label small" for="is_active_new">Active</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Create type</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
