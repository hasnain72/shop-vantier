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
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

