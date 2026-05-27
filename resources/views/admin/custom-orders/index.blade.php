@extends('admin.layouts.app')
@section('title', 'Custom Orders')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <div class="h4 mb-0">Custom Orders</div>
    <div class="text-secondary small">{{ $counts['all'] }} total enquiries</div>
  </div>
</div>

{{-- Status tabs --}}
<ul class="nav nav-tabs mb-3">
  @php($tab = request('status', ''))
  <li class="nav-item">
    <a class="nav-link {{ $tab === '' ? 'active' : '' }}"
       href="{{ route('admin.custom-orders.index') }}">
      All <span class="badge bg-secondary ms-1">{{ $counts['all'] }}</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ $tab === 'new' ? 'active' : '' }}"
       href="{{ route('admin.custom-orders.index', ['status' => 'new']) }}">
      New <span class="badge bg-danger ms-1">{{ $counts['new'] }}</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ $tab === 'read' ? 'active' : '' }}"
       href="{{ route('admin.custom-orders.index', ['status' => 'read']) }}">
      Read <span class="badge bg-warning text-dark ms-1">{{ $counts['read'] }}</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ $tab === 'replied' ? 'active' : '' }}"
       href="{{ route('admin.custom-orders.index', ['status' => 'replied']) }}">
      Replied <span class="badge bg-info text-dark ms-1">{{ $counts['replied'] }}</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ $tab === 'closed' ? 'active' : '' }}"
       href="{{ route('admin.custom-orders.index', ['status' => 'closed']) }}">
      Closed <span class="badge bg-secondary ms-1">{{ $counts['closed'] }}</span>
    </a>
  </li>
</ul>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.custom-orders.index') }}">
  @if(request('status'))
    <input type="hidden" name="status" value="{{ request('status') }}">
  @endif
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
      <div class="row g-2 align-items-end">
        <div class="col-md-4">
          <input type="text" name="search" class="form-control form-control-sm"
            placeholder="Name, email or phone…" value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
          <input type="date" name="from" class="form-control form-control-sm"
            placeholder="From" value="{{ request('from') }}">
        </div>
        <div class="col-md-2">
          <input type="date" name="to" class="form-control form-control-sm"
            placeholder="To" value="{{ request('to') }}">
        </div>
        <div class="col-md-2">
          <select name="sort" class="form-select form-select-sm">
            <option value="newest"   @selected(request('sort','newest') === 'newest')>Newest first</option>
            <option value="oldest"   @selected(request('sort') === 'oldest')>Oldest first</option>
            <option value="name-asc" @selected(request('sort') === 'name-asc')>Name A–Z</option>
          </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
          <button class="btn btn-sm btn-primary w-50"><i class="bi bi-search"></i></button>
          <a class="btn btn-sm btn-outline-secondary w-50"
             href="{{ route('admin.custom-orders.index') }}"><i class="bi bi-x-lg"></i></a>
        </div>
      </div>
    </div>
  </div>
</form>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Message</th>
          <th>Status</th>
          <th>Received</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($customOrders as $co)
          <tr class="{{ $co->status === 'new' ? 'table-danger' : '' }}">
            <td class="text-secondary small">{{ $co->id }}</td>
            <td class="fw-semibold">{{ $co->name }}</td>
            <td>
              <a href="mailto:{{ $co->email }}" class="text-decoration-none">{{ $co->email }}</a>
            </td>
            <td>{{ $co->phone ?? '—' }}</td>
            <td class="text-truncate" style="max-width:220px;" title="{{ $co->message }}">
              {{ Str::limit($co->message, 60) }}
            </td>
            <td>
              <span class="{{ $co->statusBadgeClass() }}">{{ ucfirst($co->status) }}</span>
            </td>
            <td class="text-secondary small text-nowrap">
              {{ $co->created_at->format('d M Y, H:i') }}
            </td>
            <td class="text-end text-nowrap">
              <a href="{{ route('admin.custom-orders.show', $co) }}"
                 class="btn btn-sm btn-outline-primary">View</a>
              <form method="POST" action="{{ route('admin.custom-orders.destroy', $co) }}"
                    class="d-inline" onsubmit="return confirm('Delete this enquiry?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-secondary py-4">No custom orders found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">
  {{ $customOrders->links() }}
</div>
@endsection
