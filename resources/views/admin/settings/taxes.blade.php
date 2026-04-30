@extends('admin.layouts.app')
@section('title', 'Tax Settings')

@section('content')
<div class="h4 mb-3">Taxes</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Global tax configuration --}}
<form method="POST" action="{{ route('admin.settings.taxes.update') }}">
  @csrf @method('PUT')

  <div class="card border-0 shadow-sm mb-4" style="max-width:640px;">
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
        <label class="form-label small fw-semibold">Default tax rate (%)</label>
        <div class="input-group" style="max-width:160px;">
          <input type="number" name="tax_rate" class="form-control" step="0.01" min="0" max="100"
            value="{{ $settings['tax_rate'] ?? '0' }}">
          <span class="input-group-text">%</span>
        </div>
        <div class="text-secondary small mt-1">Fallback rate when no country-specific rate matches.</div>
      </div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary mb-5">Save tax settings</button>
</form>

{{-- Country / province tax rates --}}
<div class="d-flex align-items-center justify-content-between mb-3" style="max-width:800px;">
  <div>
    <div class="fw-semibold">Country &amp; province rates</div>
    <div class="text-secondary small">{{ $taxRates->count() }} rate{{ $taxRates->count() !== 1 ? 's' : '' }} configured</div>
  </div>
  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRateModal">
    <i class="bi bi-plus-lg me-1"></i>Add rate
  </button>
</div>

<div class="card border-0 shadow-sm mb-4" style="max-width:800px;">
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead class="table-light">
        <tr>
          <th>Country</th>
          <th>Province</th>
          <th>Name</th>
          <th>Rate</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($taxRates as $tr)
          <tr>
            <td><span class="badge text-bg-secondary">{{ $tr->country_code }}</span></td>
            <td>{{ $tr->province_code ?? '–' }}</td>
            <td>{{ $tr->name }}</td>
            <td>{{ number_format($tr->rate * 100, 2) }}%</td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-secondary"
                data-bs-toggle="modal" data-bs-target="#editRateModal{{ $tr->id }}">Edit</button>
              <form method="POST" action="{{ route('admin.settings.tax-rates.destroy', $tr) }}"
                class="d-inline"
                onsubmit="return confirm('Delete this tax rate?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>

          {{-- Edit modal --}}
          <div class="modal fade" id="editRateModal{{ $tr->id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <form method="POST" action="{{ route('admin.settings.tax-rates.update', $tr) }}">
                  @csrf @method('PUT')
                  <div class="modal-header">
                    <h5 class="modal-title">Edit tax rate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row g-3">
                      <div class="col-6">
                        <label class="form-label small">Country code <span class="text-danger">*</span></label>
                        <input type="text" name="country_code" class="form-control form-control-sm text-uppercase"
                          maxlength="2" value="{{ old('country_code', $tr->country_code) }}" required>
                        <div class="form-text">ISO 3166-1 alpha-2 (e.g. US, GB, PK)</div>
                      </div>
                      <div class="col-6">
                        <label class="form-label small">Province code</label>
                        <input type="text" name="province_code" class="form-control form-control-sm text-uppercase"
                          maxlength="10" value="{{ old('province_code', $tr->province_code) }}">
                        <div class="form-text">Leave blank for country-wide</div>
                      </div>
                      <div class="col-12">
                        <label class="form-label small">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm"
                          value="{{ old('name', $tr->name) }}" required>
                      </div>
                      <div class="col-6">
                        <label class="form-label small">Rate (%) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                          <input type="number" name="rate" class="form-control" step="0.01" min="0" max="100"
                            value="{{ old('rate', number_format($tr->rate * 100, 2)) }}" required>
                          <span class="input-group-text">%</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @empty
          <tr><td colspan="5" class="text-center text-secondary py-4">No country-specific rates yet. Add one above.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Add rate modal --}}
<div class="modal fade" id="addRateModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.settings.tax-rates.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add tax rate</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-6">
              <label class="form-label small">Country code <span class="text-danger">*</span></label>
              <input type="text" name="country_code" class="form-control form-control-sm text-uppercase"
                maxlength="2" placeholder="US" required>
              <div class="form-text">ISO 3166-1 alpha-2 (e.g. US, GB, PK)</div>
            </div>
            <div class="col-6">
              <label class="form-label small">Province code</label>
              <input type="text" name="province_code" class="form-control form-control-sm text-uppercase"
                maxlength="10" placeholder="CA">
              <div class="form-text">Leave blank for country-wide</div>
            </div>
            <div class="col-12">
              <label class="form-label small">Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control form-control-sm"
                placeholder="e.g. California Sales Tax" required>
            </div>
            <div class="col-6">
              <label class="form-label small">Rate (%) <span class="text-danger">*</span></label>
              <div class="input-group input-group-sm">
                <input type="number" name="rate" class="form-control" step="0.01" min="0" max="100"
                  placeholder="7.25" required>
                <span class="input-group-text">%</span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Add rate</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
