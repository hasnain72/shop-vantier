@extends('admin.layouts.app')
@section('title', $faq ? 'Edit FAQ' : 'New FAQ')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Home Page</div>
</div>

{{-- Tab nav --}}
<ul class="nav nav-tabs mb-4">
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.settings') }}">Settings</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.craft-cards') }}">Craft Cards</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.workshop') }}">Workshop Items</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.reviews') }}">Reviews</a></li>
  <li class="nav-item"><a class="nav-link active" href="{{ route('admin.home.faqs') }}">FAQs</a></li>
</ul>

<div class="row">
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white fw-semibold">
        {{ $faq ? 'Edit FAQ' : 'New FAQ' }}
      </div>
      <div class="card-body">

        <form method="POST"
              action="{{ $faq
                ? route('admin.home.faqs.update', $faq)
                : route('admin.home.faqs.store') }}">
          @csrf
          @if($faq) @method('PUT') @endif

          @if($errors->any())
            <div class="alert alert-danger mb-3">
              <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
              </ul>
            </div>
          @endif

          {{-- Questions --}}
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Question (English) <span class="text-danger">*</span></label>
              <input name="question_en" class="form-control @error('question_en') is-invalid @enderror"
                     value="{{ old('question_en', $faq->question_en ?? '') }}" required>
              @error('question_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Question (Arabic) <span class="text-danger">*</span></label>
              <input name="question_ar" dir="rtl"
                     class="form-control @error('question_ar') is-invalid @enderror"
                     value="{{ old('question_ar', $faq->question_ar ?? '') }}" required>
              @error('question_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Answers --}}
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Answer (English) <span class="text-danger">*</span></label>
              <textarea name="answer_en" rows="5"
                        class="form-control @error('answer_en') is-invalid @enderror"
                        required>{{ old('answer_en', $faq->answer_en ?? '') }}</textarea>
              @error('answer_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Answer (Arabic) <span class="text-danger">*</span></label>
              <textarea name="answer_ar" rows="5" dir="rtl"
                        class="form-control @error('answer_ar') is-invalid @enderror"
                        required>{{ old('answer_ar', $faq->answer_ar ?? '') }}</textarea>
              @error('answer_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Sort & active --}}
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Sort Order</label>
              <input name="sort_order" type="number" min="0"
                     class="form-control @error('sort_order') is-invalid @enderror"
                     value="{{ old('sort_order', $faq->sort_order ?? 0) }}">
              @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 d-flex align-items-end pb-1">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                       id="isActiveSwitch"
                       {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="isActiveSwitch">Show on home page</label>
              </div>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              {{ $faq ? 'Save Changes' : 'Create FAQ' }}
            </button>
            <a href="{{ route('admin.home.faqs') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
@endsection
