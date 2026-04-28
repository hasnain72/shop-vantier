<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body>
    <div class="d-flex" style="min-height: 100vh;">
      <aside class="text-bg-dark p-3" style="width: 260px;">
        <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center mb-3 mb-md-4 text-white text-decoration-none">
          <span class="fs-5 fw-semibold">Laravel Shop</span>
        </a>

        <ul class="nav nav-pills flex-column gap-1">
          <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
              Dashboard
            </a>
          </li>
          <li class="nav-item"><a href="#" class="nav-link text-white">Orders</a></li>
          <li class="nav-item"><a href="{{ route('admin.products.index') }}" class="nav-link text-white {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">Products</a></li>
          <li class="nav-item"><a href="{{ route('admin.collections.index') }}" class="nav-link text-white {{ request()->routeIs('admin.collections.*') ? 'active' : '' }}">Collections</a></li>
          <li class="nav-item"><a href="#" class="nav-link text-white">Customers</a></li>
          <li class="nav-item"><a href="#" class="nav-link text-white">Discounts</a></li>
          <li class="nav-item"><a href="#" class="nav-link text-white">Inventory</a></li>
          <li class="nav-item"><a href="#" class="nav-link text-white">Shipping</a></li>
          <li class="nav-item"><a href="#" class="nav-link text-white">Reports</a></li>
          <li class="nav-item"><a href="#" class="nav-link text-white">Settings</a></li>
        </ul>
      </aside>

      <div class="flex-grow-1">
        <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
          <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav" aria-controls="topNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="topNav">
              <form class="d-flex ms-auto me-3" role="search">
                <input class="form-control" type="search" placeholder="Search" aria-label="Search">
              </form>
              <form method="POST" action="{{ route('admin.logout') }}" class="d-flex">
                @csrf
                <button class="btn btn-outline-secondary btn-sm" type="submit">Logout</button>
              </form>
            </div>
          </div>
        </nav>

        <div class="container-fluid px-4 py-3">
          @yield('breadcrumb')
          @include('admin.partials.alert')
          @yield('content')
        </div>
      </div>
    </div>
    @stack('scripts')
  </body>
</html>

