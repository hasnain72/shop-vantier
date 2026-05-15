@extends('admin.layouts.auth')

@section('title', 'Admin Login')

@section('content')
  <div class="container-fluid">
    <div class="row min-vh-100">
      <div class="col-lg-5 d-none d-lg-block p-0 position-relative bg-dark overflow-hidden">
        <video
          autoplay
          muted
          loop
          playsinline
          preload="metadata"
          style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; display:block;"
        >
          <source src="https://cdn.shopify.com/videos/c/o/v/7d1f0eae88b746e9a8594d855f300f25.mp4" type="video/mp4">
        </video>
        <div style="position:absolute; inset:0; background: linear-gradient(90deg, rgba(0,0,0,0.35), rgba(0,0,0,0.15) 45%, rgba(0,0,0,0.0));"></div>
      </div>
      <div class="col-lg-7 d-flex align-items-center justify-content-center">
        <div class="card shadow-sm border-0" style="max-width: 420px; width: 100%;">
          <div class="card-body p-4">
            <div class="text-center mb-3">
              <div class="fw-semibold" style="font-size: 1.25rem; letter-spacing: .18em; text-transform: uppercase; color: #1a1d2e;">
                Vantier
              </div>
              <div class="text-secondary small" style="letter-spacing: .06em;">Admin sign-in</div>
            </div>
            <h1 class="h4 mb-3">Sign in</h1>

            <form method="POST" action="{{ route('admin.login.post') }}">
              @csrf
              <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input
                  id="email"
                  name="email"
                  type="email"
                  value="{{ old('email') }}"
                  class="form-control @error('email') is-invalid @enderror"
                  required
                  autofocus
                >
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input
                  id="password"
                  name="password"
                  type="password"
                  class="form-control @error('password') is-invalid @enderror"
                  required
                >
                @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                  <label class="form-check-label" for="remember">
                    Remember me
                  </label>
                </div>
              </div>

              <button class="btn btn-primary w-100" type="submit">Login</button>
            </form>

            <div class="mt-3 p-3 rounded" style="background:#f8f9fa; border:1px dashed #dee2e6;">
              <div class="small fw-semibold text-secondary mb-1" style="letter-spacing:.04em;">DEMO CREDENTIALS</div>
              <div class="small mb-1">
                <span class="text-muted">Email:</span>
                <code class="text-dark ms-1">admin@store.com</code>
              </div>
              <div class="small">
                <span class="text-muted">Password:</span>
                <code class="text-dark ms-1">password</code>
              </div>
            </div>

            <div class="mt-3 text-center">
              <a href="{{ route('api.docs') }}" target="_blank" class="small text-decoration-none text-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                  <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5zm6 8H5a1 1 0 0 0 0 2h6a1 1 0 0 0 0-2z"/>
                  <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V7zm2-1a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H2z"/>
                </svg>
                API Documentation
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

