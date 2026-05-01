@php
  $authLabel = match($auth ?? null) {
    'required' => ['Required', 'auth-required'],
    'optional' => ['Optional auth', 'auth-optional'],
    'staff'    => ['Staff only', 'auth-staff'],
    default    => null,
  };
@endphp

<div class="endpoint-card" id="{{ $id }}">
  <div class="endpoint-header" data-bs-toggle="collapse" data-bs-target="#body-{{ $id }}">
    <span class="method method-{{ $method }}">{{ $method }}</span>
    <span class="endpoint-path">{{ $path }}</span>
    @if($authLabel)
      <span class="auth-badge {{ $authLabel[1] }} ms-1">{{ $authLabel[0] }}</span>
    @endif
    <span class="endpoint-summary d-none d-md-block">{{ $summary }}</span>
    <svg class="ms-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#aaa" viewBox="0 0 16 16">
      <path d="M1.5 5.5l6.5 6.5 6.5-6.5"/>
    </svg>
  </div>

  <div class="collapse" id="body-{{ $id }}">
    <div class="endpoint-body">
      @if(!empty($desc))
        <p class="mb-3" style="font-size:.875rem; color:#374151;">{{ $desc }}</p>
      @endif

      @if(!empty($params))
        <h6 class="fw-semibold mb-2" style="font-size:.8rem; text-transform:uppercase; letter-spacing:.07em; color:#6c757d;">Parameters</h6>
        <table class="table table-sm param-table mb-3">
          <thead><tr><th>Name</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
          <tbody>
            @foreach($params as $p)
              <tr>
                <td><span class="param-name">{{ $p[0] }}</span></td>
                <td><span class="param-type">{{ $p[1] }}</span></td>
                <td>{!! $p[2] ? '<span class="required-star">✱ yes</span>' : '<span class="text-muted">no</span>' !!}</td>
                <td style="font-size:.8rem; color:#374151;">{{ $p[3] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif

      @if(!empty($request))
        <h6 class="fw-semibold mb-1" style="font-size:.8rem; text-transform:uppercase; letter-spacing:.07em; color:#6c757d;">Request body</h6>
        <div class="code-block">{{ $request }}</div>
      @endif

      @if(!empty($response))
        <h6 class="fw-semibold mb-1 mt-3" style="font-size:.8rem; text-transform:uppercase; letter-spacing:.07em; color:#6c757d;">Response</h6>
        <div class="code-block">{{ $response }}</div>
      @endif
    </div>
  </div>
</div>
