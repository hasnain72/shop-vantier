@extends('admin.layouts.app')
@section('title', 'Home Page – Craft Cards')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Home Page</div>
  <a class="btn btn-primary btn-sm" href="{{ route('admin.home.craft-cards.create') }}">
    <i class="fa-solid fa-plus me-1"></i> Add Card
  </a>
</div>

{{-- Tab nav --}}
<ul class="nav nav-tabs mb-4">
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.settings') }}">Settings</a></li>
  <li class="nav-item"><a class="nav-link active" href="{{ route('admin.home.craft-cards') }}">Craft Cards</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.workshop') }}">Workshop Items</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.reviews') }}">Reviews</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.faqs') }}">FAQs</a></li>
</ul>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead class="table-light">
        <tr>
          <th style="width:60px;">Order</th>
          <th style="width:70px;">Image</th>
          <th>Title (EN)</th>
          <th>Title (AR)</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($cards as $card)
          <tr>
            <td class="text-secondary small">{{ $card->sort_order }}</td>
            <td>
              <img src="{{ $card->image_url }}" alt="" class="rounded"
                   width="50" height="50" style="object-fit:cover;"
                   onerror="this.style.display='none'">
            </td>
            <td>
              <span class="fw-semibold">{{ $card->title_en }}</span>
              <div class="text-secondary small text-truncate" style="max-width:260px;">
                {{ $card->description_en }}
              </div>
            </td>
            <td>
              <span dir="rtl">{{ $card->title_ar }}</span>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary"
                 href="{{ route('admin.home.craft-cards.edit', $card) }}">Edit</a>
              <form method="POST" action="{{ route('admin.home.craft-cards.destroy', $card) }}"
                    class="d-inline"
                    onsubmit="return confirm('Delete this craft card?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-secondary py-5">
              No craft cards yet.
              <a href="{{ route('admin.home.craft-cards.create') }}">Add the first one</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
