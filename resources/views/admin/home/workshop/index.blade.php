@extends('admin.layouts.app')
@section('title', 'Home Page – Workshop Items')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Home Page</div>
  <a class="btn btn-primary btn-sm" href="{{ route('admin.home.workshop.create') }}">
    <i class="fa-solid fa-plus me-1"></i> Add Item
  </a>
</div>

{{-- Tab nav --}}
<ul class="nav nav-tabs mb-4">
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.settings') }}">Settings</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.craft-cards') }}">Craft Cards</a></li>
  <li class="nav-item"><a class="nav-link active" href="{{ route('admin.home.workshop') }}">Workshop Items</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.reviews') }}">Reviews</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.faqs') }}">FAQs</a></li>
</ul>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead class="table-light">
        <tr>
          <th style="width:60px;">Order</th>
          <th style="width:70px;">Thumb</th>
          <th>Title (EN)</th>
          <th>Price</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $item)
          <tr>
            <td class="text-secondary small">{{ $item->sort_order }}</td>
            <td>
              <img src="{{ $item->thumb_url }}" alt="" class="rounded"
                   width="50" height="50" style="object-fit:cover;"
                   onerror="this.style.display='none'">
            </td>
            <td>
              <span class="fw-semibold">{{ $item->title_en }}</span>
              <div class="text-secondary small">{{ $item->currency }} {{ $item->current_price }}</div>
            </td>
            <td class="text-secondary small">
              {{ $item->current_price }}
              @if($item->original_price)
                <del>{{ $item->original_price }}</del>
              @endif
            </td>
            <td>
              <form method="POST" action="{{ route('admin.home.workshop.toggle', $item) }}" class="d-inline">
                @csrf
                <button type="submit"
                        class="badge border-0 {{ $item->is_active ? 'text-bg-success' : 'text-bg-secondary' }}"
                        style="cursor:pointer;">
                  {{ $item->is_active ? 'Active' : 'Hidden' }}
                </button>
              </form>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary"
                 href="{{ route('admin.home.workshop.edit', $item) }}">Edit</a>
              <form method="POST" action="{{ route('admin.home.workshop.destroy', $item) }}"
                    class="d-inline"
                    onsubmit="return confirm('Delete this workshop item?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-secondary py-5">
              No workshop items yet.
              <a href="{{ route('admin.home.workshop.create') }}">Add the first one</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
