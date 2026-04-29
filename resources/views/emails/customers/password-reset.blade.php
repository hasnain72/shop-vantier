@extends('emails.layouts.base')

@section('subject', 'Reset Your Password')

@section('content')
<h2 style="margin:0 0 16px;font-size:22px;font-weight:700;color:#1a202c;">
  Reset your password
</h2>
<p style="margin:0 0 24px;color:#4a5568;font-size:15px;line-height:1.6;">
  Hi {{ $customer->first_name }}, we received a request to reset the password for your account.
  Click the button below to choose a new password.
</p>

<div style="text-align:center;margin-bottom:28px;">
  <a href="{{ $resetUrl }}"
     style="display:inline-block;background:#5c6ac4;color:#ffffff;text-decoration:none;padding:12px 32px;border-radius:6px;font-size:15px;font-weight:600;">
    Reset Password
  </a>
</div>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#fffbeb;border-radius:6px;padding:14px 18px;margin-bottom:20px;">
  <tr>
    <td style="color:#92400e;font-size:13px;">
      This link will expire in <strong>60 minutes</strong>.
    </td>
  </tr>
</table>

<p style="margin:0;color:#718096;font-size:13px;">
  If you didn't request a password reset, you can safely ignore this email — your password won't change.
</p>

<p style="margin:16px 0 0;color:#a0aec0;font-size:12px;">
  Or copy and paste this URL into your browser:<br>
  <span style="word-break:break-all;color:#718096;">{{ $resetUrl }}</span>
</p>
@endsection
