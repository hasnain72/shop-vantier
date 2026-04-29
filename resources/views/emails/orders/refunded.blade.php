@extends('emails.layouts.base')

@section('subject', 'Refund Processed – #' . $order->order_number)

@section('content')
<h2 style="margin:0 0 16px;font-size:22px;font-weight:700;color:#1a202c;">
  Your refund has been processed
</h2>
<p style="margin:0 0 24px;color:#4a5568;font-size:15px;line-height:1.6;">
  Hi {{ $order->shipping_address['first_name'] ?? 'there' }},
  we've processed a refund for your order <strong>#{{ $order->order_number }}</strong>.
</p>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fff4;border-radius:6px;padding:20px;margin-bottom:24px;">
  <tr>
    <td style="text-align:center;">
      <p style="margin:0 0 4px;font-size:13px;color:#276749;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Refund Amount</p>
      <p style="margin:0;font-size:28px;font-weight:700;color:#22543d;">${{ number_format($refund->amount, 2) }}</p>
    </td>
  </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fb;border-radius:6px;padding:16px 20px;margin-bottom:24px;">
  <tr>
    <td style="color:#718096;font-size:13px;">Order number</td>
    <td style="text-align:right;font-weight:600;color:#1a202c;font-size:13px;">#{{ $order->order_number }}</td>
  </tr>
  <tr>
    <td style="color:#718096;font-size:13px;padding-top:6px;">Refund date</td>
    <td style="text-align:right;color:#4a5568;font-size:13px;padding-top:6px;">{{ $refund->created_at->format('M d, Y') }}</td>
  </tr>
  @if($refund->note)
  <tr>
    <td style="color:#718096;font-size:13px;padding-top:6px;">Note</td>
    <td style="text-align:right;color:#4a5568;font-size:13px;padding-top:6px;">{{ $refund->note }}</td>
  </tr>
  @endif
</table>

@if($refund->refund_line_items && count($refund->refund_line_items))
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
  <tr>
    <td style="border-bottom:2px solid #e8ecef;padding-bottom:8px;font-size:12px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.5px;">Refunded Items</td>
    <td style="border-bottom:2px solid #e8ecef;padding-bottom:8px;text-align:right;font-size:12px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.5px;">Amount</td>
  </tr>
  @foreach($refund->refund_line_items as $rli)
  <tr>
    <td style="padding:10px 0;border-bottom:1px solid #f0f0f0;color:#1a202c;font-size:14px;">
      {{ $rli->lineItem->name ?? 'Item' }} × {{ $rli->quantity }}
    </td>
    <td style="padding:10px 0;border-bottom:1px solid #f0f0f0;text-align:right;color:#1a202c;font-size:14px;">
      ${{ number_format($rli->subtotal, 2) }}
    </td>
  </tr>
  @endforeach
</table>
@endif

<p style="margin:0;color:#718096;font-size:13px;">
  Please allow 3–5 business days for the refund to appear in your original payment method.
</p>
@endsection
