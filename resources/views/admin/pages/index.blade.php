@extends('admin.layouts.app')
@section('title', 'Pages')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Pages</div>
  <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Add page
  </a>
</div>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Title</th><th>Author</th><th>Visible</th><th>Created</th><th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($pages as $page)
          <tr>
            <td class="fw-semibold">{{ $page->title }}</td>
            <td class="text-secondary small">{{ $page->author }}</td>
            <td>
              @if($page->published)
                <span class="badge text-bg-success">Visible</span>
              @else
                <span class="badge text-bg-secondary">Hidden</span>
              @endif
            </td>
            <td class="text-secondary small">{{ $page->created_at->format('M j, Y') }}</td>
            <td class="pe-3">
              <div class="d-flex gap-1 justify-content-end">
                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                <form method="POST" action="{{ route('admin.pages.destroy', $page) }}"
                  onsubmit="return confirm('Delete this page?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-secondary py-5">No pages yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($pages->hasPages())
    <div class="card-footer bg-transparent">{{ $pages->links() }}</div>
  @endif
</div>
@endsection
