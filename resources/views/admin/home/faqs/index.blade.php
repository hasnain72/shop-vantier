@extends('admin.layouts.app')
@section('title', 'Home Page – FAQs')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Home Page</div>
  <a class="btn btn-primary btn-sm" href="{{ route('admin.home.faqs.create') }}">
    <i class="fa-solid fa-plus me-1"></i> Add FAQ
  </a>
</div>

{{-- Tab nav --}}
<ul class="nav nav-tabs mb-4">
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.settings') }}">Settings</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.craft-cards') }}">Craft Cards</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.workshop') }}">Workshop Items</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.reviews') }}">Reviews</a></li>
  <li class="nav-item"><a class="nav-link active" href="{{ route('admin.home.faqs') }}">FAQs</a></li>
</ul>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead class="table-light">
        <tr>
          <th style="width:60px;">Order</th>
          <th>Question (EN)</th>
          <th>Question (AR)</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($faqs as $faq)
          <tr>
            <td class="text-secondary small">{{ $faq->sort_order }}</td>
            <td>
              <span class="fw-semibold">{{ $faq->question_en }}</span>
              <div class="text-secondary small text-truncate" style="max-width:300px;">
                {{ $faq->answer_en }}
              </div>
            </td>
            <td dir="rtl" class="small">{{ $faq->question_ar }}</td>
            <td>
              <form method="POST" action="{{ route('admin.home.faqs.toggle', $faq) }}" class="d-inline">
                @csrf
                <button type="submit"
                        class="badge border-0 {{ $faq->is_active ? 'text-bg-success' : 'text-bg-secondary' }}"
                        style="cursor:pointer;">
                  {{ $faq->is_active ? 'Active' : 'Hidden' }}
                </button>
              </form>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary"
                 href="{{ route('admin.home.faqs.edit', $faq) }}">Edit</a>
              <form method="POST" action="{{ route('admin.home.faqs.destroy', $faq) }}"
                    class="d-inline"
                    onsubmit="return confirm('Delete this FAQ?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-secondary py-5">
              No FAQs yet.
              <a href="{{ route('admin.home.faqs.create') }}">Add the first one</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
