@extends('emails.layouts.base')

@section('subject', 'Order Update – #' . $order->order_number)

@section('content')
@php
  $labels = [
    'financial_status'   => 'Payment status',
    'fulfillment_status' => 'Fulfillment status',
  ];
@endphp

<h2 style="margin:0 0 16px;font-size:22px;font-weight:700;color:#1a202c;">
  Order Update
</h2>
<p style="margin:0 0 24px;color:#4a5568;font-size:15px;line-height:1.6;">
  Hi {{ $order->shipping_address['first_name'] ?? 'there' }}, there's an update on your order
  <strong>#{{ $order->order_number }}</strong>:
</p>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fb;border-radius:6px;padding:16px 20px;margin-bottom:24px;">
  @foreach($changes as $field => $change)
    <tr>
      <td style="color:#718096;font-size:13px;padding:6px 0;">
        {{ $labels[$field] ?? ucfirst(str_replace('_',' ',$field)) }}
      </td>
      <td style="text-align:right;font-size:13px;padding:6px 0;">
        <span style="color:#a0aec0;">
          {{ strtoupper($change['old'] ?? 'unfulfilled') }}
        </span>
        <span style="color:#a0aec0;padding:0 6px;">→</span>
        <span style="background:#c6f6d5;color:#276749;padding:2px 8px;border-radius:4px;font-size:12px;font-weight:600;">
          {{ strtoupper($change['new'] ?? 'unfulfilled') }}
        </span>
      </td>
    </tr>
  @endforeach
</table>

<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
  <tr>
    <td style="color:#718096;font-size:13px;">Order number</td>
    <td style="text-align:right;font-weight:600;color:#1a202c;font-size:13px;">#{{ $order->order_number }}</td>
  </tr>
  <tr>
    <td style="color:#718096;font-size:13px;padding-top:6px;">Order total</td>
    <td style="text-align:right;color:#4a5568;font-size:13px;padding-top:6px;">${{ number_format($order->total_price, 2) }}</td>
  </tr>
</table>

<p style="margin:0;color:#718096;font-size:13px;line-height:1.6;">
  If you have any questions, just reply to this email.
</p>
@endsection
