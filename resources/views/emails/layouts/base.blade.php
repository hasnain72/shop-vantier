<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('subject', 'Email from Vantier')</title>
</head>
<body style="margin:0;padding:0;background:#f4f5f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f4f5f7;padding:40px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" role="presentation"
          style="max-width:600px;width:100%;background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);">

          {{-- Header --}}
          <tr>
            <td style="background:#1a1d2e;padding:24px 32px;">
              @php $logo = \App\Models\StoreSetting::where('key','store_logo')->value('value'); @endphp
              @if($logo)
                <img src="{{ asset('storage/'.$logo) }}" alt="Store Logo" height="40" style="display:block;">
              @else
                <span style="color:#ffffff;font-size:20px;font-weight:700;letter-spacing:-.5px;">
                  {{ \App\Models\StoreSetting::where('key','store_name')->value('value') ?? config('app.name') }}
                </span>
              @endif
            </td>
          </tr>

          {{-- Body --}}
          <tr>
            <td style="padding:32px;">
              @yield('content')
            </td>
          </tr>

          {{-- Footer --}}
          <tr>
            <td style="background:#f8f9fb;padding:20px 32px;border-top:1px solid #e8ecef;text-align:center;">
              <p style="margin:0 0 4px;color:#718096;font-size:12px;">
                {{ \App\Models\StoreSetting::where('key','store_name')->value('value') ?? config('app.name') }}
              </p>
              <p style="margin:0;color:#a0aec0;font-size:11px;">
                {{ \App\Models\StoreSetting::where('key','store_address')->value('value') }}
              </p>
              <p style="margin:8px 0 0;color:#a0aec0;font-size:11px;">
                You're receiving this because you have an account or placed an order with us.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
