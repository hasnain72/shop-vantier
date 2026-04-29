@extends('admin.layouts.app')
@section('title', 'Blog')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Blog</div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBlogModal">
    <i class="bi bi-plus-lg me-1"></i>Add blog
  </button>
</div>

<div class="row g-3">
  @forelse($blogs as $blog)
    <div class="col-md-6 col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="fw-semibold">{{ $blog->title }}</div>
          <div class="text-secondary small mt-1">{{ $blog->articles_count }} articles</div>
        </div>
        <div class="card-footer bg-transparent d-flex gap-2">
          <a href="{{ route('admin.blogs.articles', $blog) }}" class="btn btn-sm btn-primary">
            View articles
          </a>
          <form method="POST" action="{{ route('admin.blogs.destroy', $blog) }}"
            onsubmit="return confirm('Delete this blog and all articles?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Delete</button>
          </form>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12 text-center text-secondary py-5">No blogs yet.</div>
  @endforelse
</div>

{{-- Create Blog Modal --}}
<div class="modal fade" id="createBlogModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('admin.blogs.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">New blog</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label class="form-label">Blog title <span class="text-danger">*</span></label>
          <input name="title" class="form-control" required placeholder="e.g. Watch Care Tips">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create blog</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
