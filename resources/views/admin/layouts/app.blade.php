<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
      :root {
        --admin-sidebar-width: 260px;
        --admin-sidebar-collapsed-width: 76px;
      }

      .admin-sidebar .nav-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }
      .admin-sidebar .nav-link i.fa-fw {
        width: 1.25rem;
        text-align: center;
        opacity: 0.9;
      }
      .admin-sidebar .nav-link.active i {
        opacity: 1;
      }
      .admin-sidebar-logout .btn {
        font-weight: 500;
      }

      .admin-sidebar {
        width: var(--admin-sidebar-width);
        transition: width 0.18s ease;
      }
      .admin-sidebar.is-collapsed {
        width: var(--admin-sidebar-collapsed-width);
      }
      .admin-sidebar.is-collapsed .admin-sidebar-text,
      .admin-sidebar.is-collapsed .admin-sidebar-brand-text,
      .admin-sidebar.is-collapsed .admin-sidebar-logout span {
        display: none !important;
      }
      .admin-sidebar.is-collapsed .nav-link {
        justify-content: center;
        gap: 0;
      }
      .admin-sidebar.is-collapsed .nav-link i.fa-fw {
        opacity: 0.95;
      }

      .admin-topbar {
        min-height: 52px;
      }
      .admin-user-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
      }
      .admin-user-avatar {
        width: 30px;
        height: 30px;
        border-radius: 999px;
        display: grid;
        place-items: center;
        background: rgba(15, 20, 25, 0.06);
      }
    </style>
  </head>
  <body>
    <div class="d-flex" style="min-height: 100vh;">
      <aside id="adminSidebar" class="admin-sidebar text-bg-dark p-3 d-flex flex-column min-vh-100">
        <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2 mb-3 mb-md-4 text-white text-decoration-none">
          <i class="fa-solid fa-store fa-lg text-warning"></i>
          <span class="admin-sidebar-brand-text fs-5 fw-semibold">Vantier</span>
        </a>

        <ul class="nav nav-pills flex-column gap-1 flex-grow-1">
          <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
              <i class="fa-solid fa-gauge-high fa-fw" aria-hidden="true"></i>
              <span class="admin-sidebar-text">Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.orders.index') }}" class="nav-link text-white {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
              <i class="fa-solid fa-bag-shopping fa-fw" aria-hidden="true"></i>
              <span class="admin-sidebar-text">Orders</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.products.index') }}" class="nav-link text-white {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
              <i class="fa-solid fa-box fa-fw" aria-hidden="true"></i>
              <span class="admin-sidebar-text">Products</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.collections.index') }}" class="nav-link text-white {{ request()->routeIs('admin.collections.*') ? 'active' : '' }}">
              <i class="fa-solid fa-layer-group fa-fw" aria-hidden="true"></i>
              <span class="admin-sidebar-text">Collections</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.customers.index') }}" class="nav-link text-white {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
              <i class="fa-solid fa-users fa-fw" aria-hidden="true"></i>
              <span class="admin-sidebar-text">Customers</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link text-white opacity-75" onclick="return false;">
              <i class="fa-solid fa-tags fa-fw" aria-hidden="true"></i>
              <span class="admin-sidebar-text">Discounts</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.inventory.index') }}" class="nav-link text-white {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
              <i class="fa-solid fa-warehouse fa-fw" aria-hidden="true"></i>
              <span class="admin-sidebar-text">Inventory</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.locations.index') }}" class="nav-link text-white {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
              <i class="fa-solid fa-location-dot fa-fw" aria-hidden="true"></i>
              <span class="admin-sidebar-text">Locations</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.shipping.index') }}" class="nav-link text-white {{ request()->routeIs('admin.shipping.*') ? 'active' : '' }}">
              <i class="fa-solid fa-truck-fast fa-fw" aria-hidden="true"></i>
              <span class="admin-sidebar-text">Shipping</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link text-white opacity-75" onclick="return false;">
              <i class="fa-solid fa-chart-line fa-fw" aria-hidden="true"></i>
              <span class="admin-sidebar-text">Reports</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.settings.payments') }}" class="nav-link text-white {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
              <i class="fa-solid fa-gear fa-fw" aria-hidden="true"></i>
              <span class="admin-sidebar-text">Settings</span>
            </a>
          </li>
        </ul>

        <div class="admin-sidebar-logout mt-3 pt-3 border-top border-secondary border-opacity-25">
          <form method="POST" action="{{ route('admin.logout') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-outline-light w-100 d-flex align-items-center justify-content-center gap-2 py-2">
              <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
              <span>Log out</span>
            </button>
          </form>
        </div>
      </aside>

      <div class="flex-grow-1">
        <nav class="admin-topbar navbar navbar-expand-lg bg-body-tertiary border-bottom">
          <div class="container-fluid">
            <button id="adminSidebarToggle" class="btn btn-outline-secondary btn-sm me-2" type="button" aria-label="Toggle sidebar">
              <i class="fa-solid fa-bars"></i>
            </button>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav" aria-controls="topNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="topNav">
              <form class="d-flex ms-auto" role="search">
                <input class="form-control" type="search" placeholder="Search" aria-label="Search">
              </form>
            </div>

            <div class="d-flex align-items-center gap-2 ms-auto">
              <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle admin-user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <span class="admin-user-avatar" aria-hidden="true"><i class="fa-regular fa-user"></i></span>
                  <span class="d-none d-md-inline">
                    {{ auth()->user()?->name ?? auth()->user()?->email ?? 'Admin' }}
                  </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li class="px-3 py-2">
                    <div class="fw-semibold">{{ auth()->user()?->name ?? 'Admin' }}</div>
                    <div class="text-muted small">{{ auth()->user()?->email }}</div>
                  </li>
                </ul>
              </div>

              <form method="POST" action="{{ route('admin.logout') }}" class="m-0 d-flex">
                @csrf
                <button class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2" type="submit">
                  <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                  <span class="d-none d-sm-inline">Log out</span>
                </button>
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
    <script>
      (function () {
        var sidebar = document.getElementById('adminSidebar');
        var toggle = document.getElementById('adminSidebarToggle');
        if (!sidebar || !toggle) return;

        try {
          var saved = localStorage.getItem('admin_sidebar_collapsed');
          if (saved === '1') sidebar.classList.add('is-collapsed');
        } catch (e) {}

        function applyTitles() {
          var collapsed = sidebar.classList.contains('is-collapsed');
          var links = sidebar.querySelectorAll('.nav-link');
          links.forEach(function (a) {
            var text = a.querySelector('.admin-sidebar-text');
            if (!text) return;
            if (collapsed) a.setAttribute('title', text.textContent.trim());
            else a.removeAttribute('title');
          });
        }

        applyTitles();

        toggle.addEventListener('click', function () {
          sidebar.classList.toggle('is-collapsed');
          applyTitles();
          try {
            localStorage.setItem('admin_sidebar_collapsed', sidebar.classList.contains('is-collapsed') ? '1' : '0');
          } catch (e) {}
        });
      })();
    </script>
  </body>
</html>

