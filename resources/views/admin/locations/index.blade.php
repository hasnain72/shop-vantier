@extends('admin.layouts.app')
@section('title', 'Locations')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Locations</div>
  <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Add location
  </a>
</div>

<div class="row g-3">
  @forelse($locations as $location)
    <div class="col-md-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="fw-semibold">{{ $location->name }}</div>
              <div class="text-secondary small">{{ $location->address1 }}, {{ $location->city }}, {{ $location->country }}</div>
              @if($location->phone)<div class="text-secondary small">{{ $location->phone }}</div>@endif
              <div class="mt-1">
                @if($location->is_active)
                  <span class="badge text-bg-success">Active</span>
                @else
                  <span class="badge text-bg-secondary">Inactive</span>
                @endif
                @if($location->fulfills_online_orders)
                  <span class="badge text-bg-info ms-1">Fulfills online orders</span>
                @endif
              </div>
              <div class="text-secondary small mt-1">{{ $location->inventory_levels_count }} inventory items</div>
            </div>
            <div class="d-flex gap-1">
              <a href="{{ route('admin.locations.edit', $location) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
              <form method="POST" action="{{ route('admin.locations.destroy', $location) }}"
                onsubmit="return confirm('Delete this location?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12 text-center text-secondary py-5">No locations yet.</div>
  @endforelse
</div>
@endsection
