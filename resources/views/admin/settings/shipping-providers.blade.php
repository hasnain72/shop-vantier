@extends('admin.layouts.app')
@section('title', 'Shipping providers')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <div class="h4 mb-0">Shipping providers</div>
    <div class="text-secondary small">Courier integrations used at fulfillment time.</div>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('admin.settings.shipping-providers.update') }}">
  @csrf @method('PUT')

  {{-- SMSA Express --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <div class="fw-semibold fs-6">SMSA Express</div>
          <div class="text-secondary small">Saudi Arabia domestic + GCC courier</div>
        </div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" name="shipping_smsa_enabled" value="1"
            id="smsaEnabled" @checked($settings['shipping.smsa.enabled'] ?? false)>
        </div>
      </div>

      <div id="smsaFields" class="{{ ($settings['shipping.smsa.enabled'] ?? false) ? '' : 'd-none' }}">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label small">Environment</label>
            <select name="shipping_smsa_env" class="form-select form-select-sm">
              <option value="sandbox" @selected(($settings['shipping.smsa.env'] ?? 'sandbox') === 'sandbox')>Sandbox</option>
              <option value="live"    @selected(($settings['shipping.smsa.env'] ?? '') === 'live')>Live</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label small">PassKey (API token)</label>
            <input type="password" name="shipping_smsa_passkey" class="form-control form-control-sm"
              value="{{ $settings['shipping.smsa.passkey'] ?? '' }}"
              placeholder="Get from SMSA account manager">
          </div>

          <div class="col-md-3">
            <label class="form-label small">Account No.</label>
            <input name="shipping_smsa_account_no" class="form-control form-control-sm"
              value="{{ $settings['shipping.smsa.account_no'] ?? '' }}">
          </div>

          <div class="col-md-3">
            <label class="form-label small">Service code</label>
            <input name="shipping_smsa_service_code" class="form-control form-control-sm"
              value="{{ $settings['shipping.smsa.service_code'] ?? 'PDO' }}"
              placeholder="PDO / CCO / DLV">
          </div>
        </div>

        <hr class="my-3">

        <div class="fw-semibold small mb-2">Pickup / Sender details</div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small">Sender name</label>
            <input name="shipping_smsa_sender_name" class="form-control form-control-sm"
              value="{{ $settings['shipping.smsa.sender_name'] ?? '' }}">
          </div>
          <div class="col-md-6">
            <label class="form-label small">Contact person</label>
            <input name="shipping_smsa_sender_contact" class="form-control form-control-sm"
              value="{{ $settings['shipping.smsa.sender_contact'] ?? '' }}">
          </div>
          <div class="col-md-4">
            <label class="form-label small">Phone</label>
            <input name="shipping_smsa_sender_phone" class="form-control form-control-sm"
              value="{{ $settings['shipping.smsa.sender_phone'] ?? '' }}">
          </div>
          <div class="col-md-4">
            <label class="form-label small">Email</label>
            <input name="shipping_smsa_sender_email" type="email" class="form-control form-control-sm"
              value="{{ $settings['shipping.smsa.sender_email'] ?? '' }}">
          </div>
          <div class="col-md-4">
            <label class="form-label small">Pickup city</label>
            <input name="shipping_smsa_sender_city" class="form-control form-control-sm"
              value="{{ $settings['shipping.smsa.sender_city'] ?? 'Riyadh' }}">
          </div>
        </div>

        <div class="alert alert-info small mt-3 mb-0">
          For a quick setup you can also add these values to <code>.env</code>: <code>SMSA_PASSKEY</code>,
          <code>SMSA_ACCOUNT_NO</code>, and the <code>SMSA_SENDER_*</code> keys. StoreSetting overrides
          take precedence — leaving a field blank falls back to the env value.
        </div>
      </div>
    </div>
  </div>

  <button class="btn btn-primary" type="submit">Save settings</button>
</form>
@endsection

@push('scripts')
<script>
  document.getElementById('smsaEnabled')?.addEventListener('change', function () {
    document.getElementById('smsaFields').classList.toggle('d-none', !this.checked);
  });
</script>
@endpush
