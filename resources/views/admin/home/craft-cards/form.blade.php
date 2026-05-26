@extends('admin.layouts.app')
@section('title', $card ? 'Edit Craft Card' : 'New Craft Card')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Home Page</div>
</div>

{{-- Tab nav --}}
<ul class="nav nav-tabs mb-4">
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.settings') }}">Settings</a></li>
  <li class="nav-item"><a class="nav-link active" href="{{ route('admin.home.craft-cards') }}">Craft Cards</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.workshop') }}">Workshop Items</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.reviews') }}">Reviews</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.faqs') }}">FAQs</a></li>
</ul>

<div class="row">
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white fw-semibold">
        {{ $card ? 'Edit Craft Card' : 'New Craft Card' }}
      </div>
      <div class="card-body">

        <form method="POST"
              action="{{ $card
                ? route('admin.home.craft-cards.update', $card)
                : route('admin.home.craft-cards.store') }}">
          @csrf
          @if($card) @method('PUT') @endif

          @if($errors->any())
            <div class="alert alert-danger mb-3">
              <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
              </ul>
            </div>
          @endif

          {{-- Titles --}}
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Title (English) <span class="text-danger">*</span></label>
              <input name="title_en" class="form-control @error('title_en') is-invalid @enderror"
                     value="{{ old('title_en', $card->title_en ?? '') }}" required>
              @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Title (Arabic) <span class="text-danger">*</span></label>
              <input name="title_ar" dir="rtl"
                     class="form-control @error('title_ar') is-invalid @enderror"
                     value="{{ old('title_ar', $card->title_ar ?? '') }}" required>
              @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Descriptions --}}
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Description (English) <span class="text-danger">*</span></label>
              <textarea name="description_en" rows="4"
                        class="form-control @error('description_en') is-invalid @enderror"
                        required>{{ old('description_en', $card->description_en ?? '') }}</textarea>
              @error('description_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Description (Arabic) <span class="text-danger">*</span></label>
              <textarea name="description_ar" rows="4" dir="rtl"
                        class="form-control @error('description_ar') is-invalid @enderror"
                        required>{{ old('description_ar', $card->description_ar ?? '') }}</textarea>
              @error('description_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Image --}}
          <div class="mb-3">
            <label class="form-label fw-semibold">Image URL <span class="text-danger">*</span></label>
            <input name="image_url" type="url"
                   class="form-control @error('image_url') is-invalid @enderror"
                   value="{{ old('image_url', $card->image_url ?? '') }}"
                   placeholder="https://..." required>
            @error('image_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          @if($card && $card->image_url)
            <div class="mb-3">
              <img src="{{ $card->image_url }}" alt="" class="rounded border" style="max-height:120px;">
            </div>
          @endif

          {{-- Sort order --}}
          <div class="mb-4" style="max-width:160px;">
            <label class="form-label fw-semibold">Sort Order</label>
            <input name="sort_order" type="number" min="0"
                   class="form-control @error('sort_order') is-invalid @enderror"
                   value="{{ old('sort_order', $card->sort_order ?? 0) }}">
            @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              {{ $card ? 'Save Changes' : 'Create Card' }}
            </button>
            <a href="{{ route('admin.home.craft-cards') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
@endsection
