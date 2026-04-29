@extends('emails.layouts.base')

@section('subject', 'Order Cancelled – #' . $order->order_number)

@section('content')
<h2 style="margin:0 0 16px;font-size:22px;font-weight:700;color:#1a202c;">
  Your order has been cancelled
</h2>
<p style="margin:0 0 24px;color:#4a5568;font-size:15px;line-height:1.6;">
  Hi {{ $order->shipping_address['first_name'] ?? 'there' }},
  your order <strong>#{{ $order->order_number }}</strong> has been cancelled.
  @if($order->cancel_reason)
    Reason: <em>{{ ucfirst(str_replace('_', ' ', $order->cancel_reason)) }}</em>.
  @endif
</p>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fb;border-radius:6px;padding:16px 20px;margin-bottom:24px;">
  <tr>
    <td style="color:#718096;font-size:13px;">Order number</td>
    <td style="text-align:right;font-weight:600;color:#1a202c;font-size:13px;">#{{ $order->order_number }}</td>
  </tr>
  <tr>
    <td style="color:#718096;font-size:13px;padding-top:6px;">Cancelled on</td>
    <td style="text-align:right;color:#4a5568;font-size:13px;padding-top:6px;">{{ $order->cancelled_at ? $order->cancelled_at->format('M d, Y') : now()->format('M d, Y') }}</td>
  </tr>
  <tr>
    <td style="color:#718096;font-size:13px;padding-top:6px;">Total</td>
    <td style="text-align:right;font-weight:600;color:#1a202c;font-size:13px;padding-top:6px;">${{ number_format($order->total_price, 2) }}</td>
  </tr>
</table>

@if($order->financial_status === 'refunded' || $order->financial_status === 'partially_refunded')
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fff4;border-radius:6px;padding:16px 20px;margin-bottom:24px;">
  <tr>
    <td style="color:#276749;font-size:14px;">
      <strong>A refund has been issued.</strong> Please allow 3–5 business days for it to appear in your account.
    </td>
  </tr>
</table>
@endif

<p style="margin:0;color:#718096;font-size:13px;">
  If you have any questions, please reply to this email or contact our support team.
</p>
@endsection
