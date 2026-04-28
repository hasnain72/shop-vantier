@extends('admin.layouts.app')

@section('title', 'Collections')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <div class="h4 mb-0">Collections</div>
    <div class="text-secondary small">{{ $collections->total() }} collections total</div>
  </div>
  <a class="btn btn-primary" href="{{ route('admin.collections.create') }}">
    <i class="bi bi-plus-lg me-1"></i>New collection
  </a>
</div>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead class="table-light">
        <tr>
          <th style="width:56px;">Image</th>
          <th>Title</th>
          <th>Products</th>
          <th>Published</th>
          <th>Sort order</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($collections as $collection)
          <tr>
            <td>
              @if($collection->image)
                <img src="{{ asset('storage/'.$collection->image) }}" class="rounded" width="40" height="40" style="object-fit:cover;">
              @else
                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                  <i class="bi bi-collection text-secondary"></i>
                </div>
              @endif
            </td>
            <td>
              <a href="{{ route('admin.collections.edit', $collection) }}" class="fw-semibold text-decoration-none">
                {{ $collection->title }}
              </a>
              <div class="text-secondary small">{{ $collection->slug }}</div>
            </td>
            <td>
              <span class="badge text-bg-secondary">{{ $collection->products_count }}</span>
            </td>
            <td>
              @if($collection->published)
                <span class="badge text-bg-success">Published</span>
              @else
                <span class="badge text-bg-secondary">Hidden</span>
              @endif
            </td>
            <td class="text-secondary small">{{ $collection->sort_order }}</td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.collections.edit', $collection) }}">Edit</a>
              <button type="button" class="btn btn-sm btn-outline-danger"
                onclick="confirmDelete('{{ route('admin.collections.destroy', $collection) }}')">Delete</button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-secondary py-5">
              No collections yet.
              <a href="{{ route('admin.collections.create') }}">Create your first collection</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">
  {{ $collections->links() }}
</div>

<form method="POST" id="deleteForm">
  @csrf
  @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
  function confirmDelete(url) {
    Swal.fire({
      title: 'Delete this collection?',
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
