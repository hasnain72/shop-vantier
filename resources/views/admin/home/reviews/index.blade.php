@extends('admin.layouts.app')
@section('title', 'Home Page – Customer Reviews')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Home Page</div>
  <a class="btn btn-primary btn-sm" href="{{ route('admin.home.reviews.create') }}">
    <i class="fa-solid fa-plus me-1"></i> Add Review
  </a>
</div>

{{-- Tab nav --}}
<ul class="nav nav-tabs mb-4">
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.settings') }}">Settings</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.craft-cards') }}">Craft Cards</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.workshop') }}">Workshop Items</a></li>
  <li class="nav-item"><a class="nav-link active" href="{{ route('admin.home.reviews') }}">Reviews</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.faqs') }}">FAQs</a></li>
</ul>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead class="table-light">
        <tr>
          <th style="width:60px;">Order</th>
          <th style="width:70px;">Image</th>
          <th>Author</th>
          <th>Product</th>
          <th>Price</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($reviews as $review)
          <tr>
            <td class="text-secondary small">{{ $review->sort_order }}</td>
            <td>
              <img src="{{ $review->product_image_url }}" alt="" class="rounded"
                   width="50" height="50" style="object-fit:cover;"
                   onerror="this.style.display='none'">
            </td>
            <td>
              <span class="fw-semibold">{{ $review->author_name }}</span>
              <div class="text-secondary small text-truncate" style="max-width:220px;">
                {{ $review->review_en }}
              </div>
            </td>
            <td class="small">{{ $review->product_name_en }}</td>
            <td class="text-secondary small">
              {{ $review->currency_en }} {{ $review->current_price }}
              @if($review->compare_at_price)
                <del>{{ $review->compare_at_price }}</del>
              @endif
            </td>
            <td>
              <form method="POST" action="{{ route('admin.home.reviews.toggle', $review) }}" class="d-inline">
                @csrf
                <button type="submit"
                        class="badge border-0 {{ $review->is_active ? 'text-bg-success' : 'text-bg-secondary' }}"
                        style="cursor:pointer;">
                  {{ $review->is_active ? 'Active' : 'Hidden' }}
                </button>
              </form>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary"
                 href="{{ route('admin.home.reviews.edit', $review) }}">Edit</a>
              <form method="POST" action="{{ route('admin.home.reviews.destroy', $review) }}"
                    class="d-inline"
                    onsubmit="return confirm('Delete this review?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-secondary py-5">
              No reviews yet.
              <a href="{{ route('admin.home.reviews.create') }}">Add the first one</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
