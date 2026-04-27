@extends('admin.layouts.app')

@section('title', 'Collections')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <div class="h4 mb-0">Collections</div>
      <div class="text-secondary small">Manage product collections.</div>
    </div>
    <a class="btn btn-primary" href="{{ route('admin.collections.create') }}">New collection</a>
  </div>

  <div class="card shadow-sm border-0">
    <div class="table-responsive">
      <table class="table mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th>Title</th>
            <th>Slug</th>
            <th>Published</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($collections as $collection)
            <tr>
              <td class="fw-semibold">{{ $collection->title }}</td>
              <td class="text-secondary">{{ $collection->slug }}</td>
              <td>
                @if($collection->published)
                  <span class="badge text-bg-success">Yes</span>
                @else
                  <span class="badge text-bg-secondary">No</span>
                @endif
              </td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.collections.edit', $collection) }}">Edit</a>
                <form class="d-inline" method="POST" action="{{ route('admin.collections.destroy', $collection) }}" onsubmit="return confirm('Delete this collection?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-secondary py-4">No collections yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">
    {{ $collections->links() }}
  </div>
@endsection

