<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Vantier</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5/dist/min/dropzone.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
      :root {
        --primary:       #5c6ac4;
        --sidebar-bg:    #1a1d2e;
        --sidebar-text:  #a0aec0;
        --sidebar-active:#5c6ac4;
        --card-shadow:   0 2px 10px rgba(0,0,0,.08);
        --sidebar-w:     260px;
        --sidebar-w-sm:  72px;
      }

      /* ── Sidebar ──────────────────────────────────────────────────── */
      #adminSidebar {
        width: var(--sidebar-w);
        min-width: var(--sidebar-w);
        background: var(--sidebar-bg);
        transition: width .18s ease, min-width .18s ease;
        overflow-x: hidden;
        overflow-y: auto;
        flex-shrink: 0;
        height: 100vh;
        position: sticky;
        top: 0;
      }
      #adminSidebar.is-collapsed { width: var(--sidebar-w-sm); min-width: var(--sidebar-w-sm); }
      #adminSidebar.is-collapsed .admin-label,
      #adminSidebar.is-collapsed .admin-brand-text,
      #adminSidebar.is-collapsed .admin-group-label,
      #adminSidebar.is-collapsed .submenu { display: none !important; }
      #adminSidebar.is-collapsed .nav-link { justify-content: center; gap: 0; }
      #adminSidebar.is-collapsed .chevron   { display: none; }

      .nav-link { color: var(--sidebar-text) !important; font-size: .875rem; border-radius: .375rem; }
      .nav-link:hover { color: #fff !important; background: rgba(255,255,255,.07) !important; }
      .nav-link.active { color: #fff !important; background: var(--sidebar-active) !important; }
      .nav-link i.fa-fw { width: 1.2rem; text-align: center; }

      /* sub-menu */
      .submenu {
        list-style: none;
        padding-left: 1.5rem;
        margin: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height .3s ease;
      }
      .submenu.show {
        max-height: 600px;
      }
      .submenu .nav-link {
        font-size: .82rem;
        padding: .3rem .75rem;
        color: #a0aec0 !important;
        display: block;
      }
      .submenu .nav-link:hover  { color: #fff !important; background: rgba(255,255,255,.07) !important; }
      .submenu .nav-link.active { color: #fff !important; background: var(--sidebar-active) !important; }

      /* group label */
      .admin-group-label {
        font-size: .68rem; font-weight: 700; letter-spacing: .08em;
        text-transform: uppercase; color: #4a5568; padding: .5rem .75rem .2rem;
      }

      /* ── Cards ────────────────────────────────────────────────────── */
      .card { box-shadow: var(--card-shadow); }
      .card-hover:hover { transform: translateY(-1px); transition: transform .15s; }

      /* ── Top-bar ──────────────────────────────────────────────────── */
      .admin-topbar { min-height: 52px; }

      /* ── Notification badge ───────────────────────────────────────── */
      .nav-badge {
        font-size: .65rem; padding: .15em .45em;
        border-radius: 999px; vertical-align: middle; margin-left: .3rem;
      }
    </style>
  </head>
  <body>
    <div class="d-flex" style="min-height:100vh;">

      {{-- ═══ SIDEBAR ═══════════════════════════════════════════════════════ --}}
      <aside id="adminSidebar" class="text-white p-3 d-flex flex-column">

        {{-- Brand --}}
        <a href="{{ route('admin.dashboard') }}"
          class="d-flex align-items-center gap-2 mb-4 text-white text-decoration-none">
          <i class="fa-solid fa-store fa-lg text-warning flex-shrink-0"></i>
          <span class="admin-brand-text fs-5 fw-semibold">Vantier</span>
        </a>

        {{-- Nav --}}
        <ul class="nav flex-column gap-1 mb-3" id="adminNav">

          <li>
            <a href="{{ route('admin.dashboard') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
              <i class="fa-solid fa-gauge-high fa-fw"></i>
              <span class="admin-label">Dashboard</span>
            </a>
          </li>

          <li>
            <a href="{{ route('admin.orders.index') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
              <i class="fa-solid fa-bag-shopping fa-fw"></i>
              <span class="admin-label">Orders</span>
              @php $pendingOrders = \App\Models\Order::where('financial_status','pending')->count(); @endphp
              @if($pendingOrders > 0)
                <span class="badge text-bg-warning nav-badge ms-auto admin-label">{{ $pendingOrders }}</span>
              @endif
            </a>
          </li>

          <li>
            <a href="{{ route('admin.custom-orders.index') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.custom-orders.*') ? 'active' : '' }}">
              <i class="fa-solid fa-pen-to-square fa-fw"></i>
              <span class="admin-label">Custom Orders</span>
              @php $newCustomOrders = \App\Models\CustomOrder::where('status','new')->count(); @endphp
              @if($newCustomOrders > 0)
                <span class="badge text-bg-danger nav-badge ms-auto admin-label">{{ $newCustomOrders }}</span>
              @endif
            </a>
          </li>

          <li>
            <a href="{{ route('admin.newsletter.index') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}">
              <i class="fa-solid fa-envelope fa-fw"></i>
              <span class="admin-label">Newsletter</span>
              @php $totalSubs = \App\Models\NewsletterSubscriber::where('active', true)->count(); @endphp
              @if($totalSubs > 0)
                <span class="badge text-bg-secondary nav-badge ms-auto admin-label">{{ $totalSubs }}</span>
              @endif
            </a>
          </li>

          {{-- Products group --}}
          <li class="admin-group-label">Catalog</li>

          <li>
            <a href="{{ route('admin.products.index') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
              <i class="fa-solid fa-box fa-fw"></i>
              <span class="admin-label">Products</span>
            </a>
          </li>
          <li>
            <a href="{{ route('admin.collections.index') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.collections.*') ? 'active' : '' }}">
              <i class="fa-solid fa-layer-group fa-fw"></i>
              <span class="admin-label">Collections</span>
            </a>
          </li>
          <li>
            <a href="{{ route('admin.product-types.index') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.product-types.*') ? 'active' : '' }}">
              <i class="fa-solid fa-tag fa-fw"></i>
              <span class="admin-label">Product Types</span>
            </a>
          </li>
          <li>
            <a href="{{ route('admin.inventory.index') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
              <i class="fa-solid fa-warehouse fa-fw"></i>
              <span class="admin-label">Inventory</span>
            </a>
          </li>

          {{-- Customers & Discounts --}}
          <li class="admin-group-label">Store</li>

          <li>
            <a href="{{ route('admin.customers.index') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
              <i class="fa-solid fa-users fa-fw"></i>
              <span class="admin-label">Customers</span>
            </a>
          </li>
          <li>
            <a href="{{ route('admin.discounts.index') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.discounts.*') ? 'active' : '' }}">
              <i class="fa-solid fa-tags fa-fw"></i>
              <span class="admin-label">Discounts</span>
            </a>
          </li>

          {{-- Content --}}
          <li class="admin-group-label">Content</li>

          <li>
            <a href="{{ route('admin.pages.index') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
              <i class="fa-solid fa-file-lines fa-fw"></i>
              <span class="admin-label">Pages</span>
            </a>
          </li>
          <li>
            <a href="{{ route('admin.blogs.index') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.blogs.*') || request()->routeIs('admin.articles.*') ? 'active' : '' }}">
              <i class="fa-solid fa-rss fa-fw"></i>
              <span class="admin-label">Blog</span>
            </a>
          </li>
          <li>
            <a href="{{ route('admin.home.settings') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.home.*') ? 'active' : '' }}">
              <i class="fa-solid fa-house fa-fw"></i>
              <span class="admin-label">Home Page</span>
            </a>
          </li>
          <li>
            <a href="{{ route('admin.gallery.index') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
              <i class="fa-solid fa-photo-film fa-fw"></i>
              <span class="admin-label">Media Gallery</span>
            </a>
          </li>

          {{-- Analytics & Config --}}
          <li class="admin-group-label">Analytics & Config</li>

          <li>
            <a href="{{ route('admin.import.index') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.import.*') ? 'active' : '' }}">
              <i class="fa-solid fa-file-import fa-fw"></i>
              <span class="admin-label">Import</span>
            </a>
          </li>

          <li>
            <a href="{{ route('admin.reports.index') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
              <i class="fa-solid fa-chart-line fa-fw"></i>
              <span class="admin-label">Reports</span>
            </a>
          </li>

          {{-- Settings group --}}
          <li>
            <button class="nav-link d-flex align-items-center gap-2 w-100 border-0 bg-transparent
              {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.shipping.*') || request()->routeIs('admin.locations.*') || request()->routeIs('admin.webhooks.*') ? 'active' : '' }}"
              type="button" id="settingsToggleBtn" onclick="toggleSidebarMenu('settingsMenu', this)"
              aria-expanded="{{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.shipping.*') || request()->routeIs('admin.locations.*') || request()->routeIs('admin.webhooks.*') ? 'true' : 'false' }}">
              <i class="fa-solid fa-gear fa-fw"></i>
              <span class="admin-label">Settings</span>
              <i class="fa-solid fa-chevron-down ms-auto chevron" style="font-size:.65rem; transition: transform .2s;"></i>
            </button>
            <ul class="submenu {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.shipping.*') || request()->routeIs('admin.locations.*') || request()->routeIs('admin.webhooks.*') ? 'show' : '' }}" id="settingsMenu">
              <li>
                <a href="{{ route('admin.settings.store') }}"
                  class="nav-link {{ request()->routeIs('admin.settings.store') ? 'active' : '' }}">
                  Store details
                </a>
              </li>
              <li>
                <a href="{{ route('admin.settings.payments') }}"
                  class="nav-link {{ request()->routeIs('admin.settings.payments') ? 'active' : '' }}">
                  Payments
                </a>
              </li>
              <li>
                <a href="{{ route('admin.shipping.index') }}"
                  class="nav-link {{ request()->routeIs('admin.shipping.*') ? 'active' : '' }}">
                  Shipping
                </a>
              </li>
              <li>
                <a href="{{ route('admin.settings.taxes') }}"
                  class="nav-link {{ request()->routeIs('admin.settings.taxes') ? 'active' : '' }}">
                  Taxes
                </a>
              </li>
              <li>
                <a href="{{ route('admin.settings.notifications') }}"
                  class="nav-link {{ request()->routeIs('admin.settings.notifications') ? 'active' : '' }}">
                  Notifications
                </a>
              </li>
              <li>
                <a href="{{ route('admin.locations.index') }}"
                  class="nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                  Locations
                </a>
              </li>
              <li>
                <a href="{{ route('admin.webhooks.index') }}"
                  class="nav-link {{ request()->routeIs('admin.webhooks.*') ? 'active' : '' }}">
                  Webhooks
                </a>
              </li>
              <li>
                <a href="{{ route('admin.settings.checkout') }}"
                  class="nav-link {{ request()->routeIs('admin.settings.checkout') ? 'active' : '' }}">
                  Checkout
                </a>
              </li>
              <li>
                <a href="{{ route('admin.settings.scripts') }}"
                  class="nav-link {{ request()->routeIs('admin.settings.scripts') ? 'active' : '' }}">
                  Scripts & Tracking
                </a>
              </li>
              <li>
                <a href="{{ route('admin.settings.legal') }}"
                  class="nav-link {{ request()->routeIs('admin.settings.legal') ? 'active' : '' }}">
                  Legal
                </a>
              </li>
            </ul>
          </li>

          <li>
            <a href="{{ route('admin.activity.index') }}"
              class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.activity.*') ? 'active' : '' }}">
              <i class="fa-solid fa-history fa-fw"></i>
              <span class="admin-label">Activity Log</span>
            </a>
          </li>

        </ul>

        {{-- Logout --}}
        <div class="mt-3 pt-3 border-top border-white border-opacity-10">
          <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit"
              class="btn btn-sm btn-outline-light w-100 d-flex align-items-center justify-content-center gap-2">
              <i class="fa-solid fa-right-from-bracket"></i>
              <span class="admin-label">Log out</span>
            </button>
          </form>
        </div>
      </aside>

      {{-- ═══ CONTENT WRAPPER ═══════════════════════════════════════════════ --}}
      <div class="flex-grow-1 d-flex flex-column overflow-hidden">

        {{-- Top navbar --}}
        <nav class="admin-topbar navbar bg-white border-bottom sticky-top">
          <div class="container-fluid gap-2">
            <button id="adminSidebarToggle" class="btn btn-sm btn-outline-secondary flex-shrink-0">
              <i class="fa-solid fa-bars"></i>
            </button>

            <span class="fw-semibold text-secondary small d-none d-md-block">
              @yield('title', 'Dashboard')
            </span>

            <div class="ms-auto d-flex align-items-center gap-2">
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2"
                  type="button" data-bs-toggle="dropdown">
                  <i class="fa-regular fa-circle-user"></i>
                  <span class="d-none d-md-inline">{{ auth()->user()?->name ?? 'Admin' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li class="px-3 py-2 border-bottom">
                    <div class="fw-semibold small">{{ auth()->user()?->name }}</div>
                    <div class="text-muted" style="font-size:.8rem;">{{ auth()->user()?->email }}</div>
                  </li>
                  <li>
                    <form method="POST" action="{{ route('admin.logout') }}">
                      @csrf
                      <button type="submit" class="dropdown-item text-danger">
                        <i class="fa-solid fa-right-from-bracket me-2"></i>Log out
                      </button>
                    </form>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </nav>

        {{-- Page content --}}
        <div class="flex-grow-1 container-fluid px-4 py-3" style="background:#f8f9fb;">
          @include('admin.partials.breadcrumb')
          @include('admin.partials.alert')
          @yield('content')
        </div>

        {{-- Footer --}}
        <footer class="border-top bg-white text-center text-secondary py-2" style="font-size:.78rem;">
          &copy; {{ date('Y') }} Vantier Admin &mdash; Laravel {{ app()->version() }}
        </footer>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dropzone@5/dist/min/dropzone.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @stack('scripts')
    <script>
    // ── Sidebar collapse toggle ──────────────────────────────────────────────
    function toggleSidebarMenu(menuId, btn) {
      var menu = document.getElementById(menuId);
      if (!menu) return;
      var isOpen = menu.classList.toggle('show');
      btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      var chevron = btn.querySelector('.chevron');
      if (chevron) chevron.style.transform = isOpen ? 'rotate(-180deg)' : '';
      if (isOpen) {
        // scroll the submenu into view inside the sidebar
        setTimeout(function () {
          menu.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 310); // after transition completes
      }
    }

    // ── Sidebar collapse button ──────────────────────────────────────────────
    (function () {
      var sidebar = document.getElementById('adminSidebar');
      var toggle  = document.getElementById('adminSidebarToggle');
      if (!sidebar || !toggle) return;

      try { if (localStorage.getItem('sb_collapsed') === '1') sidebar.classList.add('is-collapsed'); } catch(e){}

      // Set initial chevron state
      var settingsBtn = document.getElementById('settingsToggleBtn');
      if (settingsBtn && settingsBtn.getAttribute('aria-expanded') === 'true') {
        var chevron = settingsBtn.querySelector('.chevron');
        if (chevron) chevron.style.transform = 'rotate(-180deg)';
      }

      function applyTooltips() {
        var collapsed = sidebar.classList.contains('is-collapsed');
        sidebar.querySelectorAll('.nav-link[href]').forEach(function (a) {
          var t = a.querySelector('.admin-label');
          if (collapsed && t) a.setAttribute('title', t.textContent.trim());
          else a.removeAttribute('title');
        });
      }
      applyTooltips();

      toggle.addEventListener('click', function () {
        sidebar.classList.toggle('is-collapsed');
        applyTooltips();
        try { localStorage.setItem('sb_collapsed', sidebar.classList.contains('is-collapsed') ? '1' : '0'); } catch(e){}
      });
    })();
    </script>
  </body>
</html>
