@extends('emails.layouts.base')

@section('subject', 'Order Confirmed – #' . $order->order_number)

@section('content')
<h2 style="margin:0 0 16px;font-size:22px;font-weight:700;color:#1a202c;">
  Order Confirmed!
</h2>
<p style="margin:0 0 24px;color:#4a5568;font-size:15px;line-height:1.6;">
  Hi {{ $order->shipping_address['first_name'] ?? 'there' }}, thank you for your order.
  We'll let you know once your items have shipped.
</p>

{{-- Order summary box --}}
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fb;border-radius:6px;padding:16px 20px;margin-bottom:24px;">
  <tr>
    <td style="color:#718096;font-size:13px;">Order number</td>
    <td style="text-align:right;font-weight:600;color:#1a202c;font-size:13px;">#{{ $order->order_number }}</td>
  </tr>
  <tr>
    <td style="color:#718096;font-size:13px;padding-top:6px;">Date</td>
    <td style="text-align:right;color:#4a5568;font-size:13px;padding-top:6px;">{{ $order->created_at->format('M d, Y') }}</td>
  </tr>
  <tr>
    <td style="color:#718096;font-size:13px;padding-top:6px;">Payment</td>
    <td style="text-align:right;font-size:13px;padding-top:6px;">
      <span style="background:#c6f6d5;color:#276749;padding:2px 8px;border-radius:4px;font-size:12px;font-weight:600;">
        {{ strtoupper($order->financial_status) }}
      </span>
    </td>
  </tr>
</table>

{{-- Line items --}}
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
  <tr>
    <td colspan="2" style="border-bottom:2px solid #e8ecef;padding-bottom:8px;font-size:12px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.5px;">Items Ordered</td>
  </tr>
  @foreach($order->lineItems as $item)
  <tr>
    <td style="padding:10px 0;border-bottom:1px solid #f0f0f0;color:#1a202c;font-size:14px;">
      {{ $item->name }}
      @if($item->quantity > 1)
        <span style="color:#718096;"> × {{ $item->quantity }}</span>
      @endif
    </td>
    <td style="padding:10px 0;border-bottom:1px solid #f0f0f0;text-align:right;color:#1a202c;font-size:14px;white-space:nowrap;">
      ${{ number_format($item->price * $item->quantity, 2) }}
    </td>
  </tr>
  @endforeach
  <tr>
    <td style="padding:8px 0 4px;color:#718096;font-size:13px;">Subtotal</td>
    <td style="padding:8px 0 4px;text-align:right;color:#4a5568;font-size:13px;">${{ number_format($order->subtotal_price, 2) }}</td>
  </tr>
  @if($order->total_discounts > 0)
  <tr>
    <td style="padding:4px 0;color:#718096;font-size:13px;">Discount</td>
    <td style="padding:4px 0;text-align:right;color:#48bb78;font-size:13px;">–${{ number_format($order->total_discounts, 2) }}</td>
  </tr>
  @endif
  <tr>
    <td style="padding:4px 0;color:#718096;font-size:13px;">Shipping</td>
    <td style="padding:4px 0;text-align:right;color:#4a5568;font-size:13px;">${{ number_format($order->total_shipping, 2) }}</td>
  </tr>
  @if($order->total_tax > 0)
  <tr>
    <td style="padding:4px 0;color:#718096;font-size:13px;">Tax</td>
    <td style="padding:4px 0;text-align:right;color:#4a5568;font-size:13px;">${{ number_format($order->total_tax, 2) }}</td>
  </tr>
  @endif
  <tr>
    <td style="padding:10px 0 0;font-size:16px;font-weight:700;color:#1a202c;">Total</td>
    <td style="padding:10px 0 0;text-align:right;font-size:16px;font-weight:700;color:#1a202c;">${{ number_format($order->total_price, 2) }}</td>
  </tr>
</table>

{{-- Shipping address --}}
@php $addr = $order->shipping_address; @endphp
@if($addr)
<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td style="vertical-align:top;width:50%;padding-right:12px;">
      <p style="margin:0 0 6px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#718096;">Shipping Address</p>
      <p style="margin:0;color:#4a5568;font-size:13px;line-height:1.6;">
        {{ $addr['first_name'] ?? '' }} {{ $addr['last_name'] ?? '' }}<br>
        {{ $addr['address1'] ?? '' }}{{ isset($addr['address2']) && $addr['address2'] ? ', '.$addr['address2'] : '' }}<br>
        {{ $addr['city'] ?? '' }}, {{ $addr['country'] ?? '' }}
      </p>
    </td>
    <td style="vertical-align:top;width:50%;padding-left:12px;">
      <p style="margin:0 0 6px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#718096;">Payment Method</p>
      <p style="margin:0;color:#4a5568;font-size:13px;">{{ ucfirst(str_replace('_',' ',$order->gateway ?? 'N/A')) }}</p>
    </td>
  </tr>
</table>
@endif
@endsection
