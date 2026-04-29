@extends('admin.layouts.app')
@section('title', $blog->title . ' — Articles')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <div class="h4 mb-0">{{ $blog->title }}</div>
    <div class="text-secondary small">Articles</div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">All blogs</a>
    <a href="{{ route('admin.blogs.articles.create', $blog) }}" class="btn btn-primary">
      <i class="bi bi-plus-lg me-1"></i>Write article
    </a>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>Title</th><th>Author</th><th>Status</th><th>Published</th><th></th></tr>
      </thead>
      <tbody>
        @forelse($articles as $article)
          <tr>
            <td class="fw-semibold">{{ $article->title }}</td>
            <td class="text-secondary small">{{ $article->author }}</td>
            <td>
              <span class="badge text-bg-{{ $article->published ? 'success' : 'secondary' }}">
                {{ $article->published ? 'Published' : 'Draft' }}
              </span>
            </td>
            <td class="text-secondary small">{{ $article->published_at?->format('M j, Y') ?? '—' }}</td>
            <td class="pe-3">
              <div class="d-flex gap-1 justify-content-end">
                <a href="{{ route('admin.blogs.articles.edit', [$blog, $article]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                <form method="POST" action="{{ route('admin.blogs.articles.destroy', [$blog, $article]) }}"
                  onsubmit="return confirm('Delete this article?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-secondary py-5">No articles yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($articles->hasPages())
    <div class="card-footer bg-transparent">{{ $articles->links() }}</div>
  @endif
</div>
@endsection
