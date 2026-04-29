@extends('emails.layouts.base')

@section('subject', "You've been invited to create an account")

@section('content')
@php $storeName = \App\Models\StoreSetting::where('key','store_name')->value('value') ?? config('app.name'); @endphp

<h2 style="margin:0 0 16px;font-size:22px;font-weight:700;color:#1a202c;">
  You're invited!
</h2>
<p style="margin:0 0 24px;color:#4a5568;font-size:15px;line-height:1.6;">
  Hi {{ $customer->first_name }}, you've been invited to create an account at
  <strong>{{ $storeName }}</strong>. Click the button below to set your password and activate your account.
</p>

<div style="text-align:center;margin-bottom:28px;">
  <a href="{{ $inviteUrl }}"
     style="display:inline-block;background:#5c6ac4;color:#ffffff;text-decoration:none;padding:12px 32px;border-radius:6px;font-size:15px;font-weight:600;">
    Create Account
  </a>
</div>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fb;border-radius:6px;padding:14px 18px;margin-bottom:20px;">
  <tr>
    <td style="color:#718096;font-size:13px;">Email</td>
    <td style="text-align:right;color:#1a202c;font-size:13px;">{{ $customer->email }}</td>
  </tr>
</table>

<p style="margin:0;color:#718096;font-size:13px;">
  This invitation link expires in 7 days. If you weren't expecting this, you can safely ignore it.
</p>
@endsection
