@extends('admin.layouts.app')
@section('title', 'Checkout Settings')

@section('content')
<div class="h4 mb-1">Checkout</div>
<p class="text-muted small mb-4">Customize the checkout experience for your customers.</p>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<form method="POST" action="{{ route('admin.settings.checkout.update') }}" style="max-width:680px;">
  @csrf @method('PUT')

  {{-- Customer accounts --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-transparent fw-semibold">Customer accounts</div>
    <div class="card-body">
      <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" name="checkout_guest_checkout" value="1"
          id="guestCheckout" @checked(($settings['checkout.guest_checkout'] ?? '1') === '1')>
        <label class="form-check-label" for="guestCheckout">
          <span class="fw-semibold">Allow guest checkout</span>
          <div class="text-muted small">Customers can complete checkout without creating an account.</div>
        </label>
      </div>
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="checkout_require_phone" value="1"
          id="requirePhone" @checked(($settings['checkout.require_phone'] ?? '0') === '1')>
        <label class="form-check-label" for="requirePhone">
          <span class="fw-semibold">Require phone number</span>
          <div class="text-muted small">Customers must provide a phone number at checkout.</div>
        </label>
      </div>
    </div>
  </div>

  {{-- Order options --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-transparent fw-semibold">Order options</div>
    <div class="card-body">
      <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" name="checkout_note_enabled" value="1"
          id="noteEnabled" @checked(($settings['checkout.note_enabled'] ?? '1') === '1')>
        <label class="form-check-label" for="noteEnabled">
          <span class="fw-semibold">Order notes</span>
          <div class="text-muted small">Let customers add a note to their order.</div>
        </label>
      </div>
      <div class="mb-3">
        <label class="form-label small fw-semibold">Order note placeholder text</label>
        <input name="checkout_order_notes_placeholder" class="form-control form-control-sm"
          value="{{ old('checkout_order_notes_placeholder', $settings['checkout.order_notes_placeholder'] ?? '') }}"
          placeholder="Add a note to your order…">
      </div>
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="checkout_tip_enabled" value="1"
          id="tipEnabled" @checked(($settings['checkout.tip_enabled'] ?? '0') === '1')>
        <label class="form-check-label" for="tipEnabled">
          <span class="fw-semibold">Accept tips</span>
          <div class="text-muted small">Show a tip option at checkout.</div>
        </label>
      </div>
    </div>
  </div>

  {{-- Thank you page --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-transparent fw-semibold">Post-purchase</div>
    <div class="card-body">
      <label class="form-label small fw-semibold">Thank you page message</label>
      <textarea name="checkout_thank_you_message" rows="3" class="form-control"
        placeholder="Thank you for your order! We'll send you a confirmation email shortly.">{{ old('checkout_thank_you_message', $settings['checkout.thank_you_message'] ?? '') }}</textarea>
      <div class="form-text">Displayed to customers after a successful order.</div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary">Save settings</button>
</form>
@endsection
