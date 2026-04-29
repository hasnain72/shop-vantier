@extends('admin.layouts.app')
@section('title', 'Tax Settings')

@section('content')
<div class="h4 mb-3">Taxes</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<form method="POST" action="{{ route('admin.settings.taxes.update') }}">
  @csrf @method('PUT')

  <div class="card border-0 shadow-sm mb-3" style="max-width:640px;">
    <div class="card-body">
      <div class="fw-semibold mb-3">Tax configuration</div>

      <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
        <div>
          <div class="fw-semibold small">Taxes included in prices</div>
          <div class="text-secondary small">Product prices already include tax (VAT-inclusive)</div>
        </div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" name="taxes_included" id="taxes_included"
            value="1" role="switch"
            {{ ($settings['taxes_included'] ?? '0') === '1' ? 'checked' : '' }}>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
        <div>
          <div class="fw-semibold small">Charge tax on shipping</div>
          <div class="text-secondary small">Apply tax to shipping rates</div>
        </div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" name="tax_shipping" id="tax_shipping"
            value="1" role="switch"
            {{ ($settings['tax_shipping'] ?? '0') === '1' ? 'checked' : '' }}>
        </div>
      </div>

      <div class="pt-3">
        <label class="form-label">Default tax rate (%)</label>
        <div class="input-group" style="max-width:160px;">
          <input type="number" name="tax_rate" class="form-control" step="0.01" min="0" max="100"
            value="{{ $settings['tax_rate'] ?? '10' }}">
          <span class="input-group-text">%</span>
        </div>
      </div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary">Save tax settings</button>
</form>
@endsection
