@extends('admin.layouts.app')
@section('title', 'Payment Settings')

@section('content')
<div class="h4 mb-4">Payment providers</div>

<form method="POST" action="{{ route('admin.settings.payments.update') }}">
  @csrf @method('PUT')

  {{-- Stripe --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="fw-semibold fs-6">Stripe</div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" name="payments_stripe_enabled" value="1"
            id="stripeEnabled" @checked($settings['payments.stripe.enabled'] ?? false)>
        </div>
      </div>
      <div id="stripeFields" class="{{ ($settings['payments.stripe.enabled'] ?? false) ? '' : 'd-none' }}">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small">Publishable key</label>
            <input name="payments_stripe_key" class="form-control form-control-sm"
              value="{{ $settings['payments.stripe.key'] ?? '' }}" placeholder="pk_live_…">
          </div>
          <div class="col-md-6">
            <label class="form-label small">Secret key</label>
            <input type="password" name="payments_stripe_secret" class="form-control form-control-sm"
              value="{{ $settings['payments.stripe.secret'] ?? '' }}" placeholder="sk_live_…">
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- PayPal --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="fw-semibold fs-6">PayPal</div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" name="payments_paypal_enabled" value="1"
            id="paypalEnabled" @checked($settings['payments.paypal.enabled'] ?? false)>
        </div>
      </div>
      <div id="paypalFields" class="{{ ($settings['payments.paypal.enabled'] ?? false) ? '' : 'd-none' }}">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small">Client ID</label>
            <input name="payments_paypal_client_id" class="form-control form-control-sm"
              value="{{ $settings['payments.paypal.client_id'] ?? '' }}">
          </div>
          <div class="col-md-6">
            <label class="form-label small">Client Secret</label>
            <input type="password" name="payments_paypal_client_secret" class="form-control form-control-sm"
              value="{{ $settings['payments.paypal.client_secret'] ?? '' }}">
          </div>
          <div class="col-md-4">
            <label class="form-label small">Mode</label>
            <select name="payments_paypal_mode" class="form-select form-select-sm">
              <option value="sandbox" @selected(($settings['payments.paypal.mode'] ?? 'sandbox') === 'sandbox')>Sandbox</option>
              <option value="live"    @selected(($settings['payments.paypal.mode'] ?? '') === 'live')>Live</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- COD --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="fw-semibold fs-6">Cash on Delivery (COD)</div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" name="payments_cod_enabled" value="1"
            @checked($settings['payments.cod.enabled'] ?? true)>
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label small">Additional COD fee ($)</label>
        <input name="payments_cod_fee" type="number" step="0.01" min="0"
          class="form-control form-control-sm"
          value="{{ $settings['payments.cod.fee'] ?? '0.00' }}">
      </div>
    </div>
  </div>

  {{-- Bank Transfer --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="fw-semibold fs-6">Bank Transfer</div>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" name="payments_bank_transfer_enabled" value="1"
            @checked($settings['payments.bank_transfer.enabled'] ?? false)>
        </div>
      </div>
      <div>
        <label class="form-label small">Bank account details (shown to customer)</label>
        <textarea name="payments_bank_transfer_account_details" class="form-control" rows="3">{{ $settings['payments.bank_transfer.account_details'] ?? '' }}</textarea>
      </div>
    </div>
  </div>

  <button class="btn btn-primary" type="submit">Save settings</button>
</form>
@endsection

@push('scripts')
<script>
  function toggleFields(toggleId, fieldsId) {
    document.getElementById(toggleId).addEventListener('change', function () {
      document.getElementById(fieldsId).classList.toggle('d-none', !this.checked);
    });
  }
  toggleFields('stripeEnabled', 'stripeFields');
  toggleFields('paypalEnabled', 'paypalFields');
</script>
@endpush
