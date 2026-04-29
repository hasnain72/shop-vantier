@extends('emails.layouts.base')

@section('subject', 'Low Stock Alert')

@section('content')
@php
  $variant  = $level->variant;
  $product  = $variant->product ?? null;
  $location = $level->location ?? null;
@endphp

<h2 style="margin:0 0 16px;font-size:22px;font-weight:700;color:#1a202c;">
  Low Stock Alert
</h2>
<p style="margin:0 0 24px;color:#4a5568;font-size:15px;line-height:1.6;">
  A product has fallen below the low-stock threshold and may need to be restocked.
</p>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#fff5f5;border-radius:6px;padding:16px 20px;margin-bottom:24px;">
  <tr>
    <td style="color:#718096;font-size:13px;">Product</td>
    <td style="text-align:right;font-weight:600;color:#1a202c;font-size:13px;">{{ $product->title ?? 'N/A' }}</td>
  </tr>
  @if($variant->title && $variant->title !== 'Default Title')
  <tr>
    <td style="color:#718096;font-size:13px;padding-top:6px;">Variant</td>
    <td style="text-align:right;color:#4a5568;font-size:13px;padding-top:6px;">{{ $variant->title }}</td>
  </tr>
  @endif
  <tr>
    <td style="color:#718096;font-size:13px;padding-top:6px;">SKU</td>
    <td style="text-align:right;color:#4a5568;font-size:13px;padding-top:6px;">{{ $variant->sku ?? '—' }}</td>
  </tr>
  <tr>
    <td style="color:#718096;font-size:13px;padding-top:6px;">Location</td>
    <td style="text-align:right;color:#4a5568;font-size:13px;padding-top:6px;">{{ $location->name ?? 'Unknown' }}</td>
  </tr>
  <tr>
    <td style="color:#718096;font-size:13px;padding-top:6px;">Available qty</td>
    <td style="text-align:right;font-size:13px;font-weight:700;color:#c53030;padding-top:6px;">{{ $level->available }}</td>
  </tr>
</table>

<div style="text-align:center;">
  <a href="{{ route('admin.products.edit', $product->id ?? 0) }}"
     style="display:inline-block;background:#5c6ac4;color:#ffffff;text-decoration:none;padding:10px 24px;border-radius:6px;font-size:14px;font-weight:600;">
    View Product
  </a>
</div>
@endsection
