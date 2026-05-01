<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>API Reference — v1</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --sidebar-w: 260px;
      --accent: #5c6ac4;
      --get:    #0d6efd;
      --post:   #198754;
      --put:    #fd7e14;
      --patch:  #6f42c1;
      --delete: #dc3545;
    }
    body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f8f9fb; }

    /* ── Sidebar ── */
    #sidebar {
      position: fixed; top: 0; left: 0; bottom: 0;
      width: var(--sidebar-w); overflow-y: auto;
      background: #1a1d2e; color: #c9cde0;
      padding: 0 0 2rem;
      z-index: 100;
    }
    #sidebar .brand {
      display: block; padding: 1.1rem 1.25rem;
      font-size: .85rem; font-weight: 700; letter-spacing: .14em;
      text-transform: uppercase; color: #fff;
      border-bottom: 1px solid rgba(255,255,255,.08);
      text-decoration: none;
    }
    #sidebar .section-label {
      padding: .75rem 1.25rem .25rem;
      font-size: .68rem; font-weight: 700; letter-spacing: .12em;
      text-transform: uppercase; color: #7a82a8;
    }
    #sidebar a.nav-link {
      padding: .3rem 1.25rem; font-size: .82rem;
      color: #c9cde0; border-radius: 0;
    }
    #sidebar a.nav-link:hover,
    #sidebar a.nav-link.active { background: rgba(255,255,255,.07); color: #fff; }
    #sidebar a.nav-link .badge { font-size: .62rem; padding: .22em .45em; }

    /* ── Main ── */
    #main { margin-left: var(--sidebar-w); padding: 2rem 2.5rem 4rem; max-width: 900px; }

    /* ── Section / endpoint card ── */
    .section-title {
      font-size: 1.35rem; font-weight: 700;
      border-bottom: 2px solid #e2e6ea; padding-bottom: .4rem;
      margin-top: 2.5rem; margin-bottom: 1.25rem; color: #1a1d2e;
    }
    .endpoint-card {
      background: #fff; border: 1px solid #e2e6ea;
      border-radius: 8px; margin-bottom: 1rem; overflow: hidden;
    }
    .endpoint-header {
      display: flex; align-items: center; gap: .75rem;
      padding: .75rem 1.1rem; cursor: pointer;
      user-select: none;
    }
    .endpoint-header:hover { background: #f6f8fa; }
    .endpoint-path { font-family: 'Courier New', monospace; font-size: .9rem; font-weight: 600; color: #1a1d2e; }
    .endpoint-summary { font-size: .82rem; color: #6c757d; margin-left: auto; }
    .endpoint-body { border-top: 1px solid #e2e6ea; padding: 1.1rem 1.25rem; }

    /* ── Method badge ── */
    .method { font-family: monospace; font-size: .72rem; font-weight: 700;
      padding: .28em .6em; border-radius: 4px; text-transform: uppercase; color: #fff; min-width: 56px; text-align: center; }
    .method-GET    { background: var(--get); }
    .method-POST   { background: var(--post); }
    .method-PUT    { background: var(--put); }
    .method-PATCH  { background: var(--patch); }
    .method-DELETE { background: var(--delete); }

    /* ── Auth badge ── */
    .auth-badge {
      font-size: .68rem; padding: .22em .55em; border-radius: 20px;
      border: 1px solid; font-weight: 600;
    }
    .auth-required  { color: #842029; border-color: #f5c2c7; background: #fff5f5; }
    .auth-optional  { color: #055160; border-color: #b6effb; background: #f0fcff; }
    .auth-staff     { color: #3d0a91; border-color: #d0b4fe; background: #f9f5ff; }

    /* ── Code blocks ── */
    .code-block {
      background: #1a1d2e; color: #e2e8f0;
      border-radius: 6px; padding: 1rem 1.1rem;
      font-size: .8rem; font-family: 'Courier New', monospace;
      overflow-x: auto; margin: .5rem 0;
      white-space: pre;
    }
    .param-table th { font-size: .78rem; background: #f6f8fa; }
    .param-table td { font-size: .8rem; }
    .param-name { font-family: monospace; font-weight: 600; color: #5c6ac4; }
    .param-type { color: #6c757d; font-size: .75rem; }
    .required-star { color: #dc3545; }

    /* ── Misc ── */
    .base-url-bar {
      background: #1a1d2e; color: #7dd3fc; border-radius: 8px;
      padding: .6rem 1rem; font-family: monospace; font-size: .87rem;
      margin-bottom: 1.5rem;
    }
    .hero { margin-bottom: 1.5rem; }
    .hero h1 { font-size: 1.8rem; font-weight: 800; color: #1a1d2e; }
    .hero p  { color: #6c757d; }

    @media (max-width: 768px) {
      #sidebar { display: none; }
      #main { margin-left: 0; padding: 1rem; }
    }
  </style>
</head>
<body>

<!-- ═══════════════════════════════ SIDEBAR ═══════════════════════════════ -->
<nav id="sidebar">
  <a href="#top" class="brand">&#9679; API Reference v1</a>

  <div class="section-label mt-2">General</div>
  <a href="#health"   class="nav-link"><span class="badge method-GET method me-1">GET</span> Health</a>
  <a href="#shop"     class="nav-link"><span class="badge method-GET method me-1">GET</span> Shop Info</a>

  <div class="section-label">Authentication</div>
  <a href="#auth-register"         class="nav-link"><span class="badge method-POST method me-1">POST</span> Register</a>
  <a href="#auth-login"            class="nav-link"><span class="badge method-POST method me-1">POST</span> Login</a>
  <a href="#auth-logout"           class="nav-link"><span class="badge method-POST method me-1">POST</span> Logout</a>
  <a href="#auth-me"               class="nav-link"><span class="badge method-GET method me-1">GET</span> Me</a>
  <a href="#auth-profile"          class="nav-link"><span class="badge method-PUT method me-1">PUT</span> Update Profile</a>
  <a href="#auth-change-password"  class="nav-link"><span class="badge method-PUT method me-1">PUT</span> Change Password</a>
  <a href="#auth-forgot"           class="nav-link"><span class="badge method-POST method me-1">POST</span> Forgot Password</a>
  <a href="#auth-reset"            class="nav-link"><span class="badge method-POST method me-1">POST</span> Reset Password</a>

  <div class="section-label">Customer</div>
  <a href="#customer-me"              class="nav-link"><span class="badge method-GET method me-1">GET</span> Profile</a>
  <a href="#customer-me-update"       class="nav-link"><span class="badge method-PUT method me-1">PUT</span> Update Profile</a>
  <a href="#customer-orders"          class="nav-link"><span class="badge method-GET method me-1">GET</span> My Orders</a>
  <a href="#customer-addresses"       class="nav-link"><span class="badge method-GET method me-1">GET</span> Addresses</a>
  <a href="#customer-addr-store"      class="nav-link"><span class="badge method-POST method me-1">POST</span> Add Address</a>
  <a href="#customer-addr-update"     class="nav-link"><span class="badge method-PUT method me-1">PUT</span> Update Address</a>
  <a href="#customer-addr-delete"     class="nav-link"><span class="badge method-DELETE method me-1">DEL</span> Delete Address</a>
  <a href="#customer-addr-default"    class="nav-link"><span class="badge method-PUT method me-1">PUT</span> Set Default Addr</a>

  <div class="section-label">Products</div>
  <a href="#products-index"   class="nav-link"><span class="badge method-GET method me-1">GET</span> List Products</a>
  <a href="#products-show"    class="nav-link"><span class="badge method-GET method me-1">GET</span> Get Product</a>
  <a href="#products-handle"  class="nav-link"><span class="badge method-GET method me-1">GET</span> By Handle</a>
  <a href="#products-count"   class="nav-link"><span class="badge method-GET method me-1">GET</span> Count</a>

  <div class="section-label">Collections</div>
  <a href="#collections-index"    class="nav-link"><span class="badge method-GET method me-1">GET</span> List</a>
  <a href="#collections-show"     class="nav-link"><span class="badge method-GET method me-1">GET</span> Get Collection</a>
  <a href="#collections-handle"   class="nav-link"><span class="badge method-GET method me-1">GET</span> By Handle</a>
  <a href="#collections-products" class="nav-link"><span class="badge method-GET method me-1">GET</span> Products</a>
  <a href="#collections-count"    class="nav-link"><span class="badge method-GET method me-1">GET</span> Count</a>

  <div class="section-label">Cart</div>
  <a href="#cart-show"     class="nav-link"><span class="badge method-GET method me-1">GET</span> Show Cart</a>
  <a href="#cart-add"      class="nav-link"><span class="badge method-POST method me-1">POST</span> Add Items</a>
  <a href="#cart-update"   class="nav-link"><span class="badge method-POST method me-1">POST</span> Update</a>
  <a href="#cart-remove"   class="nav-link"><span class="badge method-POST method me-1">POST</span> Remove Item</a>
  <a href="#cart-clear"    class="nav-link"><span class="badge method-POST method me-1">POST</span> Clear</a>
  <a href="#cart-discount" class="nav-link"><span class="badge method-POST method me-1">POST</span> Apply Discount</a>
  <a href="#cart-shipping" class="nav-link"><span class="badge method-GET method me-1">GET</span> Shipping Rates</a>

  <div class="section-label">Checkout</div>
  <a href="#checkout-validate" class="nav-link"><span class="badge method-POST method me-1">POST</span> Validate</a>

  <div class="section-label">Orders</div>
  <a href="#orders-store"  class="nav-link"><span class="badge method-POST method me-1">POST</span> Create Order</a>
  <a href="#orders-show"   class="nav-link"><span class="badge method-GET method me-1">GET</span> Get Order</a>
  <a href="#orders-cancel" class="nav-link"><span class="badge method-POST method me-1">POST</span> Cancel Order</a>

  <div class="section-label">Discounts</div>
  <a href="#discount-lookup" class="nav-link"><span class="badge method-POST method me-1">POST</span> Lookup Code</a>

  <div class="section-label">Shipping</div>
  <a href="#shipping-rates"    class="nav-link"><span class="badge method-GET method me-1">GET</span> Get Rates</a>
  <a href="#shipping-estimate" class="nav-link"><span class="badge method-POST method me-1">POST</span> Estimate</a>

  <div class="section-label">Search</div>
  <a href="#search" class="nav-link"><span class="badge method-GET method me-1">GET</span> Search</a>

  <div class="section-label">CMS</div>
  <a href="#pages-index"    class="nav-link"><span class="badge method-GET method me-1">GET</span> Pages</a>
  <a href="#pages-show"     class="nav-link"><span class="badge method-GET method me-1">GET</span> Get Page</a>
  <a href="#blogs-index"    class="nav-link"><span class="badge method-GET method me-1">GET</span> Blogs</a>
  <a href="#blogs-show"     class="nav-link"><span class="badge method-GET method me-1">GET</span> Get Blog</a>
  <a href="#blogs-articles" class="nav-link"><span class="badge method-GET method me-1">GET</span> Blog Articles</a>
  <a href="#articles-show"  class="nav-link"><span class="badge method-GET method me-1">GET</span> Get Article</a>

  <div class="section-label">Inventory</div>
  <a href="#inventory-index"    class="nav-link"><span class="badge method-GET method me-1">GET</span> List</a>
  <a href="#inventory-adjust"   class="nav-link"><span class="badge method-POST method me-1">POST</span> Adjust</a>
  <a href="#inventory-transfer" class="nav-link"><span class="badge method-POST method me-1">POST</span> Transfer</a>
  <a href="#inventory-history"  class="nav-link"><span class="badge method-GET method me-1">GET</span> History</a>
</nav>

<!-- ═══════════════════════════════ MAIN ═══════════════════════════════ -->
<main id="main" id="top">
  <div class="hero">
    <h1>API Reference</h1>
    <p>REST API for the Vantier storefront. All responses are JSON. Authenticated endpoints require a Bearer token obtained via <code>/auth/login</code> or <code>/auth/register</code>.</p>
  </div>

  <div class="base-url-bar">Base URL: {{ url('/api/v1') }}</div>

  <div class="p-3 mb-4 rounded" style="background:#fff3cd; border:1px solid #ffc107; font-size:.85rem;">
    <strong>Authentication:</strong> Protected endpoints require <code>Authorization: Bearer &lt;token&gt;</code> header.
    Always include <code>Accept: application/json</code>.
  </div>

  <!-- ═══ GENERAL ═══ -->
  <h2 class="section-title" id="health">General</h2>

  @include('api.partials.endpoint', [
    'id'      => 'health',
    'method'  => 'GET',
    'path'    => '/api/v1/health',
    'summary' => 'Health check',
    'auth'    => null,
    'desc'    => 'Returns OK if the API is reachable.',
    'response' => '{
  "success": true,
  "message": "OK"
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'shop',
    'method'  => 'GET',
    'path'    => '/api/v1/shop',
    'summary' => 'Store information',
    'auth'    => null,
    'desc'    => 'Returns store settings: name, email, currency, timezone, weight unit, logo, favicon, and SEO meta fields.',
    'response' => '{
  "success": true,
  "data": {
    "shop": {
      "name": "Vantier",
      "email": "store@example.com",
      "currency": "USD",
      "currency_symbol": "$",
      "timezone": "UTC",
      "weight_unit": "kg",
      "logo": "https://example.com/storage/logo.png",
      "favicon": null,
      "meta_title": "Vantier — Premium Watches",
      "meta_description": "Luxury watch accessories."
    }
  }
}',
  ])

  <!-- ═══ AUTH ═══ -->
  <h2 class="section-title" id="auth-register">Authentication</h2>

  @include('api.partials.endpoint', [
    'id'      => 'auth-register',
    'method'  => 'POST',
    'path'    => '/api/v1/auth/register',
    'summary' => 'Register a new customer',
    'auth'    => null,
    'desc'    => 'Creates a customer account and returns the customer object plus a Bearer token.',
    'params'  => [
      ['first_name',            'string',  true,  'Customer first name'],
      ['last_name',             'string',  true,  'Customer last name'],
      ['email',                 'string',  true,  'Unique email address'],
      ['password',              'string',  true,  'Min 8 characters'],
      ['password_confirmation', 'string',  true,  'Must match password'],
      ['phone',                 'string',  false, 'Optional phone number'],
    ],
    'request' => '{
  "first_name": "Jane",
  "last_name": "Doe",
  "email": "jane@example.com",
  "password": "secret123",
  "password_confirmation": "secret123"
}',
    'response' => '{
  "success": true,
  "message": "Registered",
  "data": {
    "customer": { "id": 1, "first_name": "Jane", "last_name": "Doe", "email": "jane@example.com", ... },
    "token": "1|aBcDeFgH..."
  }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'auth-login',
    'method'  => 'POST',
    'path'    => '/api/v1/auth/login',
    'summary' => 'Login',
    'auth'    => null,
    'desc'    => 'Authenticates a customer and returns a Bearer token.',
    'params'  => [
      ['email',    'string', true,  'Registered email'],
      ['password', 'string', true,  'Account password'],
    ],
    'request' => '{
  "email": "jane@example.com",
  "password": "secret123"
}',
    'response' => '{
  "success": true,
  "message": "Logged in",
  "data": {
    "customer": { "id": 1, ... },
    "token": "2|xYzAbC..."
  }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'auth-logout',
    'method'  => 'POST',
    'path'    => '/api/v1/auth/logout',
    'summary' => 'Logout',
    'auth'    => 'required',
    'desc'    => 'Revokes the current access token.',
    'response' => '{
  "success": true,
  "message": "Logged out",
  "data": null
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'auth-me',
    'method'  => 'GET',
    'path'    => '/api/v1/auth/me',
    'summary' => 'Get current customer',
    'auth'    => 'required',
    'desc'    => 'Returns the authenticated customer with their addresses.',
    'response' => '{
  "success": true,
  "data": {
    "customer": {
      "id": 1,
      "first_name": "Jane",
      "last_name": "Doe",
      "email": "jane@example.com",
      "phone": null,
      "state": "enabled",
      "addresses": []
    }
  }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'auth-profile',
    'method'  => 'PUT',
    'path'    => '/api/v1/auth/profile',
    'summary' => 'Update profile',
    'auth'    => 'required',
    'desc'    => 'Updates first name, last name, and phone of the authenticated customer.',
    'params'  => [
      ['first_name', 'string', false, 'New first name'],
      ['last_name',  'string', false, 'New last name'],
      ['phone',      'string', false, 'New phone number'],
    ],
    'request' => '{
  "first_name": "Janet",
  "phone": "+1-555-0100"
}',
    'response' => '{
  "success": true,
  "message": "Profile updated",
  "data": { "customer": { ... } }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'auth-change-password',
    'method'  => 'PUT',
    'path'    => '/api/v1/auth/change-password',
    'summary' => 'Change password',
    'auth'    => 'required',
    'desc'    => 'Changes the authenticated customer\'s password. Requires the current password for verification.',
    'params'  => [
      ['current_password',      'string', true, 'Current account password'],
      ['new_password',          'string', true, 'New password (min 8 chars)'],
      ['new_password_confirmation', 'string', true, 'Must match new_password'],
    ],
    'request' => '{
  "current_password": "secret123",
  "new_password": "newSecret456",
  "new_password_confirmation": "newSecret456"
}',
    'response' => '{
  "success": true,
  "message": "Password updated",
  "data": null
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'auth-forgot',
    'method'  => 'POST',
    'path'    => '/api/v1/auth/forgot-password',
    'summary' => 'Forgot password',
    'auth'    => null,
    'desc'    => 'Sends a password reset link to the given email address.',
    'params'  => [
      ['email', 'string', true, 'Registered customer email'],
    ],
    'request' => '{
  "email": "jane@example.com"
}',
    'response' => '{
  "success": true,
  "message": "Reset link sent",
  "data": null
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'auth-reset',
    'method'  => 'POST',
    'path'    => '/api/v1/auth/reset-password',
    'summary' => 'Reset password',
    'auth'    => null,
    'desc'    => 'Resets the customer password using the token received by email.',
    'params'  => [
      ['email',                 'string', true, 'Customer email'],
      ['token',                 'string', true, 'Token from reset email'],
      ['password',              'string', true, 'New password'],
      ['password_confirmation', 'string', true, 'Must match password'],
    ],
    'request' => '{
  "email": "jane@example.com",
  "token": "abc123...",
  "password": "newSecret456",
  "password_confirmation": "newSecret456"
}',
    'response' => '{
  "success": true,
  "message": "Password reset successful",
  "data": null
}',
  ])

  <!-- ═══ CUSTOMER ═══ -->
  <h2 class="section-title" id="customer-me">Customer</h2>
  <p class="text-muted small mb-3">All customer endpoints require <code>Authorization: Bearer &lt;token&gt;</code>.</p>

  @include('api.partials.endpoint', [
    'id'      => 'customer-me',
    'method'  => 'GET',
    'path'    => '/api/v1/customers/me',
    'summary' => 'Get profile',
    'auth'    => 'required',
    'desc'    => 'Returns the authenticated customer object including addresses.',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'customer-me-update',
    'method'  => 'PUT',
    'path'    => '/api/v1/customers/me',
    'summary' => 'Update profile',
    'auth'    => 'required',
    'desc'    => 'Updates the customer\'s name, phone, or marketing preference.',
    'params'  => [
      ['first_name',        'string',  false, 'First name'],
      ['last_name',         'string',  false, 'Last name'],
      ['phone',             'string',  false, 'Phone number'],
      ['accepts_marketing', 'boolean', false, 'Marketing opt-in'],
    ],
  ])

  @include('api.partials.endpoint', [
    'id'      => 'customer-orders',
    'method'  => 'GET',
    'path'    => '/api/v1/customers/me/orders',
    'summary' => 'List my orders',
    'auth'    => 'required',
    'desc'    => 'Returns a paginated list of the customer\'s orders.',
    'params'  => [
      ['limit',  'integer', false, 'Results per page, max 50 (default 10)'],
      ['status', 'string',  false, 'Filter by financial_status'],
      ['page',   'integer', false, 'Page number'],
    ],
    'response' => '{
  "success": true,
  "data": [
    { "id": 1001, "order_number": "1001", "name": "#1001", "financial_status": "paid",
      "fulfillment_status": null, "total_price": "149.99", "currency": "USD", "created_at": "2024-01-15T10:00:00Z" }
  ],
  "meta": { "pagination": { "total": 5, "per_page": 10, "current_page": 1, "last_page": 1 } }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'customer-addresses',
    'method'  => 'GET',
    'path'    => '/api/v1/customers/me/addresses',
    'summary' => 'List addresses',
    'auth'    => 'required',
    'desc'    => 'Returns all saved addresses for the authenticated customer.',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'customer-addr-store',
    'method'  => 'POST',
    'path'    => '/api/v1/customers/me/addresses',
    'summary' => 'Add address',
    'auth'    => 'required',
    'desc'    => 'Creates a new address. The first address is automatically set as default.',
    'params'  => [
      ['first_name',    'string', true,  'Recipient first name'],
      ['last_name',     'string', true,  'Recipient last name'],
      ['address1',      'string', true,  'Street address line 1'],
      ['address2',      'string', false, 'Street address line 2'],
      ['city',          'string', true,  'City'],
      ['province',      'string', false, 'State / province'],
      ['province_code', 'string', false, 'ISO province code'],
      ['country',       'string', true,  'Country name'],
      ['country_code',  'string', true,  '2-letter ISO country code'],
      ['zip',           'string', false, 'Postal / ZIP code'],
      ['phone',         'string', false, 'Contact phone'],
      ['company',       'string', false, 'Company name'],
    ],
    'request' => '{
  "first_name": "Jane",
  "last_name": "Doe",
  "address1": "123 Main St",
  "city": "New York",
  "country": "United States",
  "country_code": "US",
  "zip": "10001"
}',
    'response' => '{
  "success": true,
  "message": "Address added",
  "data": { "id": 5, "first_name": "Jane", "address1": "123 Main St", ... }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'customer-addr-update',
    'method'  => 'PUT',
    'path'    => '/api/v1/customers/me/addresses/{address_id}',
    'summary' => 'Update address',
    'auth'    => 'required',
    'desc'    => 'Updates a specific address. Send only the fields to change.',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'customer-addr-delete',
    'method'  => 'DELETE',
    'path'    => '/api/v1/customers/me/addresses/{address_id}',
    'summary' => 'Delete address',
    'auth'    => 'required',
    'desc'    => 'Deletes an address. Cannot delete the default address when other addresses exist.',
    'response' => '{
  "success": true,
  "message": "Address deleted",
  "data": null
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'customer-addr-default',
    'method'  => 'PUT',
    'path'    => '/api/v1/customers/me/addresses/{address_id}/default',
    'summary' => 'Set default address',
    'auth'    => 'required',
    'desc'    => 'Sets the specified address as the customer\'s default shipping address.',
    'response' => '{
  "success": true,
  "message": "Default address updated",
  "data": null
}',
  ])

  <!-- ═══ PRODUCTS ═══ -->
  <h2 class="section-title" id="products-index">Products</h2>

  @include('api.partials.endpoint', [
    'id'      => 'products-index',
    'method'  => 'GET',
    'path'    => '/api/v1/products',
    'summary' => 'List products',
    'auth'    => null,
    'desc'    => 'Returns paginated active products. Supports filtering and sorting.',
    'params'  => [
      ['limit',           'integer', false, 'Items per page, max 250 (default 20)'],
      ['page',            'integer', false, 'Page number'],
      ['collection_id',   'integer', false, 'Filter by collection'],
      ['product_type',    'string',  false, 'Filter by product type'],
      ['vendor',          'string',  false, 'Filter by vendor name'],
      ['title',           'string',  false, 'Partial title match'],
      ['sort_by',         'string',  false, 'title | created_at | price (default: created_at)'],
      ['sort_direction',  'string',  false, 'asc | desc (default: desc)'],
      ['created_at_min',  'string',  false, 'ISO 8601 date filter'],
      ['created_at_max',  'string',  false, 'ISO 8601 date filter'],
    ],
    'response' => '{
  "success": true,
  "data": {
    "products": [
      { "id": 1, "title": "Chronograph Watch", "slug": "chronograph-watch", "status": "active",
        "vendor": "Vantier", "product_type": "Watch", "price": "299.99",
        "variants": [...], "images": [...] }
    ]
  },
  "meta": {
    "pagination": { "total": 42, "per_page": 20, "current_page": 1, "last_page": 3 }
  }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'products-show',
    'method'  => 'GET',
    'path'    => '/api/v1/products/{id}',
    'summary' => 'Get product by ID',
    'auth'    => null,
    'desc'    => 'Returns a single active product by numeric ID or slug.',
    'response' => '{
  "success": true,
  "data": {
    "product": { "id": 1, "title": "Chronograph Watch", "slug": "chronograph-watch",
      "description": "...", "variants": [...], "images": [...], "collections": [...] }
  }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'products-handle',
    'method'  => 'GET',
    'path'    => '/api/v1/products/handle/{slug}',
    'summary' => 'Get product by handle',
    'auth'    => null,
    'desc'    => 'Returns a product by its URL slug / handle.',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'products-count',
    'method'  => 'GET',
    'path'    => '/api/v1/products/count',
    'summary' => 'Count products',
    'auth'    => null,
    'desc'    => 'Returns the total number of active products.',
    'response' => '{
  "success": true,
  "data": { "count": 42 }
}',
  ])

  <!-- ═══ COLLECTIONS ═══ -->
  <h2 class="section-title" id="collections-index">Collections</h2>

  @include('api.partials.endpoint', [
    'id'      => 'collections-index',
    'method'  => 'GET',
    'path'    => '/api/v1/collections',
    'summary' => 'List collections',
    'auth'    => null,
    'desc'    => 'Returns all published collections.',
    'response' => '{
  "success": true,
  "data": {
    "collections": [
      { "id": 1, "title": "Best Sellers", "slug": "best-sellers", "description": "...", "published": true }
    ]
  }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'collections-show',
    'method'  => 'GET',
    'path'    => '/api/v1/collections/{id}',
    'summary' => 'Get collection',
    'auth'    => null,
    'desc'    => 'Returns a single collection by ID.',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'collections-handle',
    'method'  => 'GET',
    'path'    => '/api/v1/collections/handle/{slug}',
    'summary' => 'Get collection by handle',
    'auth'    => null,
    'desc'    => 'Returns a collection by its URL slug.',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'collections-products',
    'method'  => 'GET',
    'path'    => '/api/v1/collections/{id}/products',
    'summary' => 'List collection products',
    'auth'    => null,
    'desc'    => 'Returns paginated active products belonging to the given collection.',
    'params'  => [
      ['limit', 'integer', false, 'Items per page, max 250 (default 20)'],
      ['page',  'integer', false, 'Page number'],
    ],
  ])

  @include('api.partials.endpoint', [
    'id'      => 'collections-count',
    'method'  => 'GET',
    'path'    => '/api/v1/collections/count',
    'summary' => 'Count collections',
    'auth'    => null,
    'desc'    => 'Returns the total number of published collections.',
    'response' => '{
  "success": true,
  "data": { "count": 8 }
}',
  ])

  <!-- ═══ CART ═══ -->
  <h2 class="section-title" id="cart-show">Cart</h2>
  <p class="text-muted small mb-3">
    Cart works for both guests and authenticated customers.
    Guests must send <code>X-Cart-Token: &lt;uuid&gt;</code> header to persist their cart.
    The token is returned in <code>cart_token</code> on first add. Authenticated customers'
    carts are tied to their account automatically.
  </p>

  @include('api.partials.endpoint', [
    'id'      => 'cart-show',
    'method'  => 'GET',
    'path'    => '/api/v1/cart',
    'summary' => 'Get cart',
    'auth'    => 'optional',
    'desc'    => 'Returns the current cart. Returns 404 if no cart exists for the token or customer.',
    'response' => '{
  "success": true,
  "data": {
    "token": "550e8400-e29b-41d4-a716-446655440000",
    "note": null,
    "attributes": {},
    "currency": "USD",
    "item_count": 2,
    "total_price": 449.98,
    "total_discount": 0.0,
    "requires_shipping": true,
    "items": [
      { "id": 10, "variant_id": 3, "product_id": 1, "title": "Chronograph Watch",
        "variant_title": "Black / 42mm", "sku": "CHR-BLK-42",
        "image": "https://...", "price": 299.99, "quantity": 1, "total_price": 299.99, "properties": {} }
    ]
  }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'cart-add',
    'method'  => 'POST',
    'path'    => '/api/v1/cart/add',
    'summary' => 'Add items',
    'auth'    => 'optional',
    'desc'    => 'Adds one or more items to the cart. If the variant already exists its quantity is incremented. Returns 422 if stock is insufficient.',
    'params'  => [
      ['items',                  'array',   true,  'Array of items to add'],
      ['items.*.variant_id',     'integer', true,  'Product variant ID'],
      ['items.*.quantity',       'integer', true,  'Quantity to add (min 1)'],
      ['items.*.properties',     'object',  false, 'Key-value custom properties'],
    ],
    'request' => '{
  "items": [
    { "variant_id": 3, "quantity": 1 },
    { "variant_id": 7, "quantity": 2, "properties": { "engraving": "Happy Birthday" } }
  ]
}',
    'response' => '{
  "success": true,
  "message": "Item(s) added to cart.",
  "cart_token": "550e8400-...",
  "data": { ... }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'cart-update',
    'method'  => 'POST',
    'path'    => '/api/v1/cart/update',
    'summary' => 'Update quantities',
    'auth'    => 'optional',
    'desc'    => 'Sets the quantity of one or more line items. Pass <code>0</code> to remove an item.',
    'params'  => [
      ['updates',   'object', true, 'Map of line_item_id → new_quantity'],
    ],
    'request' => '{
  "updates": { "10": 3, "11": 0 }
}',
    'response' => '{
  "success": true,
  "data": { ... }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'cart-remove',
    'method'  => 'POST',
    'path'    => '/api/v1/cart/remove',
    'summary' => 'Remove item',
    'auth'    => 'optional',
    'desc'    => 'Removes a single line item from the cart.',
    'params'  => [
      ['line_item_id', 'integer', true, 'ID of the cart line item to remove'],
    ],
    'request' => '{
  "line_item_id": 10
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'cart-clear',
    'method'  => 'POST',
    'path'    => '/api/v1/cart/clear',
    'summary' => 'Clear cart',
    'auth'    => 'optional',
    'desc'    => 'Removes all items from the cart.',
    'response' => '{
  "success": true,
  "message": "Cart cleared.",
  "data": { "token": "...", "item_count": 0, "total_price": 0, "items": [] }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'cart-discount',
    'method'  => 'POST',
    'path'    => '/api/v1/cart/apply-discount',
    'summary' => 'Apply discount code',
    'auth'    => 'optional',
    'desc'    => 'Validates a discount code against the cart and stores it on the cart attributes if valid.',
    'params'  => [
      ['discount_code', 'string', true, 'The discount / coupon code'],
    ],
    'request' => '{
  "discount_code": "SAVE10"
}',
    'response' => '{
  "applied": true,
  "discount_amount": 30.00,
  "error": null
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'cart-shipping',
    'method'  => 'GET',
    'path'    => '/api/v1/cart/shipping-rates',
    'summary' => 'Get cart shipping rates',
    'auth'    => 'optional',
    'desc'    => 'Returns available shipping rates for the cart based on the destination.',
    'params'  => [
      ['country_code',  'string', true,  '2-letter ISO country code'],
      ['province_code', 'string', false, 'Province / state code'],
      ['zip',           'string', false, 'Postal code'],
    ],
    'response' => '{
  "success": true,
  "data": {
    "shipping_rates": [
      { "name": "Standard Shipping", "price": 5.99, "min_delivery_days": 3, "max_delivery_days": 7 }
    ]
  }
}',
  ])

  <!-- ═══ CHECKOUT ═══ -->
  <h2 class="section-title" id="checkout-validate">Checkout</h2>

  @include('api.partials.endpoint', [
    'id'      => 'checkout-validate',
    'method'  => 'POST',
    'path'    => '/api/v1/checkout/validate',
    'summary' => 'Validate checkout',
    'auth'    => 'optional',
    'desc'    => 'Calculates subtotal, discount, shipping, and tax for a given cart and shipping address. Use this before rendering the order summary page.',
    'params'  => [
      ['cart_token',                     'string', true,  'Cart token (UUID)'],
      ['shipping_address',               'object', true,  'Shipping address object'],
      ['shipping_address.country_code',  'string', true,  '2-letter ISO country code'],
      ['discount_code',                  'string', false, 'Optional discount code to preview'],
    ],
    'request' => '{
  "cart_token": "550e8400-e29b-41d4-a716-446655440000",
  "shipping_address": { "country_code": "US" },
  "discount_code": "SAVE10"
}',
    'response' => '{
  "success": true,
  "data": {
    "subtotal": 299.99,
    "total_discounts": 30.00,
    "total_shipping": 5.99,
    "total_tax": 27.00,
    "total": 302.98,
    "shipping_rates_available": true,
    "shipping_rates": [...]
  }
}',
  ])

  <!-- ═══ ORDERS ═══ -->
  <h2 class="section-title" id="orders-store">Orders</h2>

  @include('api.partials.endpoint', [
    'id'      => 'orders-store',
    'method'  => 'POST',
    'path'    => '/api/v1/orders',
    'summary' => 'Create order',
    'auth'    => null,
    'desc'    => 'Places a new order. Works for both guest and authenticated customers. Returns 422 if any item is out of stock.',
    'params'  => [
      ['customer_id',                  'integer', false, 'Authenticated customer ID'],
      ['email',                        'string',  false, 'Guest email'],
      ['phone',                        'string',  false, 'Contact phone'],
      ['line_items',                   'array',   true,  'Array of order items'],
      ['line_items.*.variant_id',      'integer', true,  'Product variant ID'],
      ['line_items.*.quantity',        'integer', true,  'Quantity (min 1)'],
      ['line_items.*.properties',      'object',  false, 'Custom line item properties'],
      ['shipping_address',             'object',  true,  'Shipping address (see address fields)'],
      ['billing_address',              'object',  false, 'Billing address (defaults to shipping)'],
      ['discount_code',                'string',  false, 'Discount code to apply'],
      ['note',                         'string',  false, 'Order note'],
      ['buyer_accepts_marketing',      'boolean', false, 'Marketing consent'],
    ],
    'request' => '{
  "email": "jane@example.com",
  "line_items": [
    { "variant_id": 3, "quantity": 1 }
  ],
  "shipping_address": {
    "first_name": "Jane", "last_name": "Doe",
    "address1": "123 Main St", "city": "New York",
    "country": "United States", "country_code": "US", "zip": "10001"
  }
}',
    'response' => '{
  "success": true,
  "message": "Order created",
  "data": {
    "order": {
      "id": 1001, "order_number": "1001", "name": "#1001",
      "email": "jane@example.com",
      "financial_status": "pending",
      "fulfillment_status": null,
      "subtotal_price": "299.99",
      "total_price": "305.98",
      "currency": "USD",
      "line_items": [...],
      "shipping_address": {...}
    }
  }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'orders-show',
    'method'  => 'GET',
    'path'    => '/api/v1/orders/{id}',
    'summary' => 'Get order',
    'auth'    => 'required',
    'desc'    => 'Returns an order by ID or order number. Authenticated customers can only see their own orders.',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'orders-cancel',
    'method'  => 'POST',
    'path'    => '/api/v1/orders/{id}/cancel',
    'summary' => 'Cancel order',
    'auth'    => 'required',
    'desc'    => 'Cancels an order. Only orders with <code>financial_status = pending</code> that have not been fulfilled can be cancelled.',
    'response' => '{
  "success": true,
  "message": "Order cancelled",
  "data": { "order": { "id": 1001, "financial_status": "voided", ... } }
}',
  ])

  <!-- ═══ DISCOUNTS ═══ -->
  <h2 class="section-title" id="discount-lookup">Discounts</h2>

  @include('api.partials.endpoint', [
    'id'      => 'discount-lookup',
    'method'  => 'POST',
    'path'    => '/api/v1/discount_codes/lookup',
    'summary' => 'Lookup discount code',
    'auth'    => null,
    'desc'    => 'Validates a discount code and returns the discount amount for the given line items. Does not apply the discount.',
    'params'  => [
      ['code',                   'string',  true,  'Discount code'],
      ['line_items',             'array',   true,  'Array of items to apply discount to'],
      ['line_items.*.variant_id','integer', true,  'Variant ID'],
      ['line_items.*.quantity',  'integer', true,  'Quantity'],
      ['customer_id',            'integer', false, 'Customer ID for per-customer limit check'],
    ],
    'request' => '{
  "code": "SAVE10",
  "line_items": [
    { "variant_id": 3, "quantity": 1 }
  ]
}',
    'response' => '{
  "discount_code": {
    "code": "SAVE10",
    "amount": 30.00,
    "value_type": "percentage",
    "value": 10,
    "minimum_amount": null,
    "minimum_quantity": null
  },
  "error": null
}',
  ])

  <!-- ═══ SHIPPING ═══ -->
  <h2 class="section-title" id="shipping-rates">Shipping</h2>

  @include('api.partials.endpoint', [
    'id'      => 'shipping-rates',
    'method'  => 'GET',
    'path'    => '/api/v1/shipping/rates',
    'summary' => 'Get shipping rates',
    'auth'    => null,
    'desc'    => 'Returns available shipping rates for a list of items and a destination address.',
    'params'  => [
      ['shipping_address',               'object',  true,  'Destination address'],
      ['shipping_address.country_code',  'string',  true,  '2-letter ISO country code'],
      ['shipping_address.province_code', 'string',  false, 'Province code'],
      ['line_items',                     'array',   true,  'Items to ship'],
      ['line_items.*.variant_id',        'integer', true,  'Variant ID'],
      ['line_items.*.quantity',          'integer', true,  'Quantity'],
    ],
    'response' => '{
  "shipping_rates": [
    { "name": "Standard", "price": 5.99 },
    { "name": "Express",  "price": 14.99 }
  ]
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'shipping-estimate',
    'method'  => 'POST',
    'path'    => '/api/v1/shipping/estimate',
    'summary' => 'Estimate shipping',
    'auth'    => null,
    'desc'    => 'Same as GET /shipping/rates but accepts a POST body — useful when the address data is too long for query params.',
  ])

  <!-- ═══ SEARCH ═══ -->
  <h2 class="section-title" id="search">Search</h2>

  @include('api.partials.endpoint', [
    'id'      => 'search',
    'method'  => 'GET',
    'path'    => '/api/v1/search',
    'summary' => 'Search',
    'auth'    => null,
    'desc'    => 'Full-text search across products, collections, pages, and articles. Returns a unified results array.',
    'params'  => [
      ['q', 'string', true, 'Search query (min 1 character)'],
    ],
    'response' => '{
  "success": true,
  "data": {
    "results": [
      { "type": "product",    "id": 1, "title": "Chronograph Watch", "url": "/products/chronograph-watch" },
      { "type": "collection", "id": 2, "title": "Best Sellers",      "url": "/collections/best-sellers" },
      { "type": "page",       "id": 3, "title": "About Us",          "url": "/pages/about-us" }
    ]
  }
}',
  ])

  <!-- ═══ CMS ═══ -->
  <h2 class="section-title" id="pages-index">CMS — Pages &amp; Blogs</h2>

  @include('api.partials.endpoint', [
    'id'      => 'pages-index',
    'method'  => 'GET',
    'path'    => '/api/v1/pages',
    'summary' => 'List pages',
    'auth'    => null,
    'desc'    => 'Returns all published CMS pages.',
    'response' => '{
  "success": true,
  "data": {
    "pages": [
      { "id": 1, "title": "About Us", "slug": "about-us", "body_html": "...", "published_at": "..." }
    ]
  }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'pages-show',
    'method'  => 'GET',
    'path'    => '/api/v1/pages/{handle}',
    'summary' => 'Get page',
    'auth'    => null,
    'desc'    => 'Returns a single page by its slug / handle.',
    'response' => '{
  "success": true,
  "data": { "page": { "id": 1, "title": "About Us", "slug": "about-us", "body_html": "..." } }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'blogs-index',
    'method'  => 'GET',
    'path'    => '/api/v1/blogs',
    'summary' => 'List blogs',
    'auth'    => null,
    'desc'    => 'Returns all blogs.',
    'response' => '{
  "success": true,
  "data": { "blogs": [ { "id": 1, "title": "News", "slug": "news" } ] }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'blogs-show',
    'method'  => 'GET',
    'path'    => '/api/v1/blogs/{handle}',
    'summary' => 'Get blog',
    'auth'    => null,
    'desc'    => 'Returns a single blog by its slug.',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'blogs-articles',
    'method'  => 'GET',
    'path'    => '/api/v1/blogs/{handle}/articles',
    'summary' => 'Blog articles',
    'auth'    => null,
    'desc'    => 'Returns all published articles belonging to the specified blog.',
    'response' => '{
  "success": true,
  "data": {
    "articles": [
      { "id": 1, "title": "Watch Cleaning Guide", "slug": "watch-cleaning", "published_at": "..." }
    ]
  }
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'articles-show',
    'method'  => 'GET',
    'path'    => '/api/v1/articles/{id}',
    'summary' => 'Get article',
    'auth'    => null,
    'desc'    => 'Returns a single blog article by ID.',
    'response' => '{
  "success": true,
  "data": {
    "article": { "id": 1, "title": "Watch Cleaning Guide", "body_html": "...", "author": "...", "published_at": "..." }
  }
}',
  ])

  <!-- ═══ INVENTORY ═══ -->
  <h2 class="section-title" id="inventory-index">Inventory <span class="badge auth-staff" style="font-size:.7rem; vertical-align:middle;">Staff only</span></h2>
  <p class="text-muted small mb-3">All inventory endpoints require a staff Sanctum token (<code>auth:sanctum</code>).</p>

  @include('api.partials.endpoint', [
    'id'      => 'inventory-index',
    'method'  => 'GET',
    'path'    => '/api/v1/inventory',
    'summary' => 'List inventory',
    'auth'    => 'staff',
    'desc'    => 'Returns all inventory items across all locations.',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'inventory-adjust',
    'method'  => 'POST',
    'path'    => '/api/v1/inventory/adjust',
    'summary' => 'Adjust inventory',
    'auth'    => 'staff',
    'desc'    => 'Adjusts the inventory quantity for a specific item at a specific location.',
    'params'  => [
      ['inventory_item_id', 'integer', true,  'Inventory item ID'],
      ['location_id',       'integer', true,  'Location ID'],
      ['adjustment',        'integer', true,  'Positive or negative quantity change'],
    ],
    'request' => '{
  "inventory_item_id": 5,
  "location_id": 1,
  "adjustment": -3
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'inventory-transfer',
    'method'  => 'POST',
    'path'    => '/api/v1/inventory/transfer',
    'summary' => 'Transfer inventory',
    'auth'    => 'staff',
    'desc'    => 'Transfers stock from one location to another.',
    'params'  => [
      ['inventory_item_id',  'integer', true, 'Inventory item ID'],
      ['from_location_id',   'integer', true, 'Source location'],
      ['to_location_id',     'integer', true, 'Destination location'],
      ['quantity',           'integer', true, 'Quantity to transfer (min 1)'],
    ],
    'request' => '{
  "inventory_item_id": 5,
  "from_location_id": 1,
  "to_location_id": 2,
  "quantity": 10
}',
  ])

  @include('api.partials.endpoint', [
    'id'      => 'inventory-history',
    'method'  => 'GET',
    'path'    => '/api/v1/inventory/{item}/history',
    'summary' => 'Inventory history',
    'auth'    => 'staff',
    'desc'    => 'Returns the adjustment history log for a specific inventory item.',
  ])

  <div class="mt-5 pt-3 border-top text-muted small text-center">
    Vantier API Reference &mdash; All responses include <code>"success": true/false</code>.<br>
    Errors return <code>{ "success": false, "message": "...", "errors": {} }</code> with an appropriate HTTP status code.
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Highlight active nav link on scroll
  const links = document.querySelectorAll('#sidebar a.nav-link');
  const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        links.forEach(l => l.classList.remove('active'));
        const a = document.querySelector(`#sidebar a[href="#${e.target.id}"]`);
        if (a) a.classList.add('active');
      }
    });
  }, { threshold: 0.4 });

  document.querySelectorAll('[id]').forEach(el => observer.observe(el));
</script>
</body>
</html>
