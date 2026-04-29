@extends('admin.layouts.app')
@section('title', 'Notifications')

@section('content')
<div class="h4 mb-3">Notification settings</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<form method="POST" action="{{ route('admin.settings.notifications.update') }}">
  @csrf @method('PUT')

  {{-- Notification email --}}
  <div class="card border-0 shadow-sm mb-4" style="max-width:640px;">
    <div class="card-body">
      <label class="form-label fw-semibold">Staff notification email</label>
      <input name="notification_email" type="email" class="form-control"
        value="{{ \App\Models\StoreSetting::where('key','notification_email')->value('value') }}"
        placeholder="admin@yourstore.com">
      <div class="form-text">Where to send low-stock alerts and new-order notifications.</div>
    </div>
  </div>

  @php
  $notifConfig = [
    'notif_order_confirmation' => [
      'label'    => 'Order confirmation',
      'audience' => 'Customer',
      'desc'     => 'Sent to customer when a new order is placed.',
    ],
    'notif_order_shipped' => [
      'label'    => 'Order shipped',
      'audience' => 'Customer',
      'desc'     => 'Sent when fulfillment tracking is added.',
    ],
    'notif_abandoned_cart' => [
      'label'    => 'Abandoned cart reminder',
      'audience' => 'Customer',
      'desc'     => 'Sent 1 hour after cart is abandoned (requires queue).',
    ],
    'notif_refund_confirmation' => [
      'label'    => 'Refund confirmation',
      'audience' => 'Customer',
      'desc'     => 'Sent when a refund is processed.',
    ],
    'notif_new_order' => [
      'label'    => 'New order',
      'audience' => 'Staff',
      'desc'     => 'Sent to staff when a new order arrives.',
    ],
    'notif_low_stock' => [
      'label'    => 'Low stock alert',
      'audience' => 'Staff',
      'desc'     => 'Sent when inventory falls below threshold.',
    ],
  ];
  @endphp

  <div class="d-flex flex-column gap-3" style="max-width:640px;">
    @foreach($notifConfig as $key => $cfg)
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center justify-content-between gap-3">
          <div>
            <div class="fw-semibold small">
              {{ $cfg['label'] }}
              <span class="badge text-bg-{{ $cfg['audience'] === 'Staff' ? 'secondary' : 'info' }} ms-1">
                {{ $cfg['audience'] }}
              </span>
            </div>
            <div class="text-secondary small">{{ $cfg['desc'] }}</div>
          </div>
          <div class="d-flex align-items-center gap-3 flex-shrink-0">
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" name="{{ $key }}"
                id="{{ $key }}" value="1" role="switch"
                {{ ($notifs[$key] ?? '1') === '1' ? 'checked' : '' }}>
              <label class="form-check-label" for="{{ $key }}"></label>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary">Edit template</button>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <button type="submit" class="btn btn-primary mt-3">Save</button>
</form>
@endsection
