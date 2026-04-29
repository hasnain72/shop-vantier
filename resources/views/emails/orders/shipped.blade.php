@extends('emails.layouts.base')

@section('subject', 'Your Order Has Shipped – #' . $order->order_number)

@section('content')
<h2 style="margin:0 0 16px;font-size:22px;font-weight:700;color:#1a202c;">
  Your order is on its way!
</h2>
<p style="margin:0 0 24px;color:#4a5568;font-size:15px;line-height:1.6;">
  Hi {{ $order->shipping_address['first_name'] ?? 'there' }},
  great news — your order <strong>#{{ $order->order_number }}</strong> has been shipped.
</p>

@if($fulfillment->tracking_number)
<table width="100%" cellpadding="0" cellspacing="0" style="background:#ebf4ff;border-radius:6px;padding:20px;margin-bottom:24px;text-align:center;">
  <tr>
    <td>
      <p style="margin:0 0 6px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#4299e1;">Tracking Number</p>
      <p style="margin:0 0 12px;font-size:20px;font-weight:700;color:#2b6cb0;letter-spacing:1px;">{{ $fulfillment->tracking_number }}</p>
      @if($fulfillment->tracking_company)
        <p style="margin:0 0 12px;color:#4a5568;font-size:13px;">via {{ $fulfillment->tracking_company }}</p>
      @endif
      @if($fulfillment->tracking_url)
        <a href="{{ $fulfillment->tracking_url }}"
           style="display:inline-block;background:#3182ce;color:#ffffff;text-decoration:none;padding:10px 24px;border-radius:6px;font-size:14px;font-weight:600;">
          Track Package
        </a>
      @endif
    </td>
  </tr>
</table>
@endif

{{-- Items in shipment --}}
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
  <tr>
    <td style="border-bottom:2px solid #e8ecef;padding-bottom:8px;font-size:12px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.5px;">Items Shipped</td>
  </tr>
  @foreach($fulfillment->lineItems as $item)
  <tr>
    <td style="padding:10px 0;border-bottom:1px solid #f0f0f0;color:#1a202c;font-size:14px;">
      {{ $item->name }}
      @if($item->quantity > 1)
        <span style="color:#718096;"> × {{ $item->quantity }}</span>
      @endif
    </td>
  </tr>
  @endforeach
</table>

@php $addr = $order->shipping_address; @endphp
@if($addr)
<p style="margin:0 0 6px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#718096;">Shipping To</p>
<p style="margin:0;color:#4a5568;font-size:13px;line-height:1.6;">
  {{ $addr['first_name'] ?? '' }} {{ $addr['last_name'] ?? '' }}<br>
  {{ $addr['address1'] ?? '' }}<br>
  {{ $addr['city'] ?? '' }}, {{ $addr['country'] ?? '' }}
</p>
@endif
@endsection
