@extends('admin.layouts.app')
@section('title', 'Scripts & Tracking')

@section('content')
<div class="h4 mb-1">Scripts & Tracking</div>
<p class="text-muted small mb-4">Add analytics, pixels, and custom code to your storefront.</p>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<form method="POST" action="{{ route('admin.settings.scripts.update') }}">
  @csrf @method('PUT')

  {{-- Quick integrations --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-transparent fw-semibold">Quick integrations</div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label small fw-semibold">
            <i class="fab fa-google text-danger me-1"></i>Google Analytics ID
          </label>
          <input name="scripts_ga_id" class="form-control form-control-sm font-monospace"
            value="{{ old('scripts_ga_id', $settings['scripts.ga_id'] ?? '') }}"
            placeholder="G-XXXXXXXXXX">
          <div class="form-text">GA4 Measurement ID</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-semibold">
            <i class="fab fa-facebook text-primary me-1"></i>Facebook Pixel ID
          </label>
          <input name="scripts_fb_pixel" class="form-control form-control-sm font-monospace"
            value="{{ old('scripts_fb_pixel', $settings['scripts.fb_pixel'] ?? '') }}"
            placeholder="123456789012345">
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-semibold">
            <i class="fab fa-google text-info me-1"></i>Google Tag Manager ID
          </label>
          <input name="scripts_gtm_id" class="form-control form-control-sm font-monospace"
            value="{{ old('scripts_gtm_id', $settings['scripts.gtm_id'] ?? '') }}"
            placeholder="GTM-XXXXXXX">
        </div>
      </div>
    </div>
  </div>

  {{-- Custom scripts --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-transparent fw-semibold">Custom code</div>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label small fw-semibold">
          Additional <code>&lt;head&gt;</code> code
          <span class="badge bg-secondary-subtle text-secondary ms-1">injected before &lt;/head&gt;</span>
        </label>
        <textarea name="scripts_head" rows="6" class="form-control font-monospace"
          style="font-size:12px;" placeholder="<!-- Custom meta tags, CSS links, etc. -->">{{ old('scripts_head', $settings['scripts.head'] ?? '') }}</textarea>
      </div>
      <div>
        <label class="form-label small fw-semibold">
          End of <code>&lt;body&gt;</code> code
          <span class="badge bg-secondary-subtle text-secondary ms-1">injected before &lt;/body&gt;</span>
        </label>
        <textarea name="scripts_body" rows="6" class="form-control font-monospace"
          style="font-size:12px;" placeholder="<!-- Custom scripts, chat widgets, etc. -->">{{ old('scripts_body', $settings['scripts.body'] ?? '') }}</textarea>
      </div>
    </div>
  </div>

  <div class="alert alert-warning small mb-3">
    <i class="fas fa-exclamation-triangle me-2"></i>
    Only add code from trusted sources. Malicious scripts can compromise your customers' data.
  </div>

  <button type="submit" class="btn btn-primary">Save scripts</button>
</form>
@endsection
