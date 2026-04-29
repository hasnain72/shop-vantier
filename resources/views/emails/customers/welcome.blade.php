@extends('emails.layouts.base')

@section('subject', 'Welcome!')

@section('content')
@php $storeName = \App\Models\StoreSetting::where('key','store_name')->value('value') ?? config('app.name'); @endphp

<h2 style="margin:0 0 16px;font-size:22px;font-weight:700;color:#1a202c;">
  Welcome to {{ $storeName }}!
</h2>
<p style="margin:0 0 24px;color:#4a5568;font-size:15px;line-height:1.6;">
  Hi {{ $customer->first_name }}, we're excited to have you on board.
  Your account has been created and you're ready to start shopping.
</p>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fb;border-radius:6px;padding:16px 20px;margin-bottom:28px;">
  <tr>
    <td style="color:#718096;font-size:13px;">Name</td>
    <td style="text-align:right;color:#1a202c;font-size:13px;">{{ $customer->first_name }} {{ $customer->last_name }}</td>
  </tr>
  <tr>
    <td style="color:#718096;font-size:13px;padding-top:6px;">Email</td>
    <td style="text-align:right;color:#1a202c;font-size:13px;padding-top:6px;">{{ $customer->email }}</td>
  </tr>
</table>

<div style="text-align:center;margin-bottom:24px;">
  <a href="{{ url('/') }}"
     style="display:inline-block;background:#5c6ac4;color:#ffffff;text-decoration:none;padding:12px 32px;border-radius:6px;font-size:15px;font-weight:600;">
    Start Shopping
  </a>
</div>

<p style="margin:0;color:#a0aec0;font-size:12px;text-align:center;">
  If you didn't create this account, please ignore this email.
</p>
@endsection
