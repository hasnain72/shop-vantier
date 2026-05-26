@extends('admin.layouts.app')
@section('title', $review ? 'Edit Review' : 'New Review')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Home Page</div>
</div>

{{-- Tab nav --}}
<ul class="nav nav-tabs mb-4">
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.settings') }}">Settings</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.craft-cards') }}">Craft Cards</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.workshop') }}">Workshop Items</a></li>
  <li class="nav-item"><a class="nav-link active" href="{{ route('admin.home.reviews') }}">Reviews</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.faqs') }}">FAQs</a></li>
</ul>

<div class="row">
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white fw-semibold">
        {{ $review ? 'Edit Customer Review' : 'New Customer Review' }}
      </div>
      <div class="card-body">

        <form method="POST"
              action="{{ $review
                ? route('admin.home.reviews.update', $review)
                : route('admin.home.reviews.store') }}">
          @csrf
          @if($review) @method('PUT') @endif

          @if($errors->any())
            <div class="alert alert-danger mb-3">
              <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
              </ul>
            </div>
          @endif

          {{-- Author --}}
          <div class="mb-3">
            <label class="form-label fw-semibold">Author Name <span class="text-danger">*</span></label>
            <input name="author_name" class="form-control @error('author_name') is-invalid @enderror"
                   value="{{ old('author_name', $review->author_name ?? '') }}" required>
            @error('author_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- Review text --}}
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Review (English) <span class="text-danger">*</span></label>
              <textarea name="review_en" rows="4"
                        class="form-control @error('review_en') is-invalid @enderror"
                        required>{{ old('review_en', $review->review_en ?? '') }}</textarea>
              @error('review_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Review (Arabic) <span class="text-danger">*</span></label>
              <textarea name="review_ar" rows="4" dir="rtl"
                        class="form-control @error('review_ar') is-invalid @enderror"
                        required>{{ old('review_ar', $review->review_ar ?? '') }}</textarea>
              @error('review_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Product image --}}
          <div class="mb-3">
            <label class="form-label fw-semibold">Product Image URL <span class="text-danger">*</span></label>
            <input name="product_image_url" type="url"
                   class="form-control @error('product_image_url') is-invalid @enderror"
                   value="{{ old('product_image_url', $review->product_image_url ?? '') }}"
                   placeholder="https://..." required>
            @error('product_image_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          @if($review && $review->product_image_url)
            <div class="mb-3">
              <img src="{{ $review->product_image_url }}" alt="" class="rounded border" style="max-height:100px;">
            </div>
          @endif

          {{-- Product name --}}
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Product Name (English) <span class="text-danger">*</span></label>
              <input name="product_name_en" class="form-control @error('product_name_en') is-invalid @enderror"
                     value="{{ old('product_name_en', $review->product_name_en ?? '') }}" required>
              @error('product_name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Product Name (Arabic) <span class="text-danger">*</span></label>
              <input name="product_name_ar" dir="rtl"
                     class="form-control @error('product_name_ar') is-invalid @enderror"
                     value="{{ old('product_name_ar', $review->product_name_ar ?? '') }}" required>
              @error('product_name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Pricing --}}
          <div class="row g-3 mb-3">
            <div class="col-md-3">
              <label class="form-label fw-semibold">Currency (EN) <span class="text-danger">*</span></label>
              <input name="currency_en" class="form-control @error('currency_en') is-invalid @enderror"
                     value="{{ old('currency_en', $review->currency_en ?? 'USD') }}" required>
              @error('currency_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Currency (AR) <span class="text-danger">*</span></label>
              <input name="currency_ar" dir="rtl"
                     class="form-control @error('currency_ar') is-invalid @enderror"
                     value="{{ old('currency_ar', $review->currency_ar ?? 'دولار') }}" required>
              @error('currency_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Current Price <span class="text-danger">*</span></label>
              <input name="current_price" class="form-control @error('current_price') is-invalid @enderror"
                     value="{{ old('current_price', $review->current_price ?? '') }}" required>
              @error('current_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Compare-at Price <span class="text-muted fw-normal">(opt.)</span></label>
              <input name="compare_at_price" class="form-control @error('compare_at_price') is-invalid @enderror"
                     value="{{ old('compare_at_price', $review->compare_at_price ?? '') }}">
              @error('compare_at_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Sort & active --}}
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Sort Order</label>
              <input name="sort_order" type="number" min="0"
                     class="form-control @error('sort_order') is-invalid @enderror"
                     value="{{ old('sort_order', $review->sort_order ?? 0) }}">
              @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 d-flex align-items-end pb-1">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                       id="isActiveSwitch"
                       {{ old('is_active', $review->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="isActiveSwitch">Show on home page</label>
              </div>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              {{ $review ? 'Save Changes' : 'Create Review' }}
            </button>
            <a href="{{ route('admin.home.reviews') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
@endsection
