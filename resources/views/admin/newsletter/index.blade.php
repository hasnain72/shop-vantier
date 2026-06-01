@extends('admin.layouts.app')
@section('title', 'Newsletter Subscribers')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <div class="h4 mb-0">Newsletter Subscribers</div>
    <div class="text-secondary small">{{ $subscribers->total() }} total</div>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show py-2">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Filters --}}
<form method="GET" class="row g-2 mb-3">
  <div class="col-auto">
    <input type="text" name="q" class="form-control form-control-sm" placeholder="Search email / name…" value="{{ request('q') }}">
  </div>
  <div class="col-auto">
    <select name="status" class="form-select form-select-sm">
      <option value="">All statuses</option>
      <option value="active" @selected(request('status') === 'active')>Active</option>
      <option value="inactive" @selected(request('status') === 'inactive')>Unsubscribed</option>
    </select>
  </div>
  <div class="col-auto">
    <button class="btn btn-sm btn-secondary" type="submit">Filter</button>
    <a href="{{ route('admin.newsletter.index') }}" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
  </div>
</form>

<div class="card shadow-sm border-0">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0 small">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Email</th>
          <th>Name</th>
          <th>Status</th>
          <th>Source</th>
          <th>Subscribed</th>
          <th>Unsubscribed</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($subscribers as $sub)
          <tr>
            <td class="text-secondary">{{ $sub->id }}</td>
            <td>{{ $sub->email }}</td>
            <td>{{ $sub->name ?? '—' }}</td>
            <td>
              @if($sub->active)
                <span class="badge bg-success">Active</span>
              @else
                <span class="badge bg-secondary">Unsubscribed</span>
              @endif
            </td>
            <td>{{ $sub->source }}</td>
            <td>{{ $sub->subscribed_at?->format('d M Y') }}</td>
            <td>{{ $sub->unsubscribed_at?->format('d M Y') ?? '—' }}</td>
            <td class="text-end">
              <form method="POST" action="{{ route('admin.newsletter.destroy', $sub) }}" onsubmit="return confirm('Delete this subscriber?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger py-0 px-2">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="text-center text-secondary py-4">No subscribers yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">{{ $subscribers->links() }}</div>
@endsection
