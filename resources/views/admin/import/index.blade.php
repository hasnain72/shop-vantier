@extends('admin.layouts.app')

@section('title', 'Import Data')

@section('content')
<div class="container-fluid py-4">

  {{-- Header --}}
  <div class="mb-4">
    <h4 class="mb-1 fw-semibold">Import Data</h4>
    <p class="text-muted small mb-0">Upload Shopify CSV exports to import products, orders &amp; inventory in one click.</p>
  </div>

  {{-- ── Result banner ───────────────────────────────────────── --}}
  @if (session('import_done'))
    @php
      $stats  = session('import_stats',  []);
      $impErr = session('import_error');
    @endphp

    <div class="card border-0 shadow-sm mb-4"
         style="border-left: 4px solid {{ $impErr ? '#dc3545' : '#198754' }} !important;">
      <div class="card-body py-3 px-4">

        <div class="d-flex align-items-center gap-2 mb-3">
          @if ($impErr)
            <i class="fa-solid fa-circle-exclamation text-danger"></i>
            <span class="fw-semibold text-danger">Import finished with errors</span>
          @else
            <i class="fa-solid fa-circle-check text-success"></i>
            <span class="fw-semibold text-success">Import complete!</span>
          @endif
        </div>

        {{-- Per-section stat cards --}}
        <div class="row g-3 mb-3">

          {{-- Products --}}
          @if (isset($stats['products']))
            <div class="col-sm-4">
              <div class="rounded p-3" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                <div class="d-flex align-items-center gap-2 mb-1">
                  <i class="fa-solid fa-box text-success"></i>
                  <span class="fw-semibold small">Products</span>
                </div>
                @if (isset($stats['products']['error']))
                  <span class="text-danger small">{{ $stats['products']['error'] }}</span>
                @else
                  <div class="small text-muted">
                    <span class="text-success fw-bold">{{ $stats['products']['created'] }}</span> created &nbsp;·&nbsp;
                    <span class="text-warning fw-bold">{{ $stats['products']['skipped'] }}</span> skipped
                  </div>
                @endif
              </div>
            </div>
          @endif

          {{-- Inventory --}}
          @if (isset($stats['inventory']))
            <div class="col-sm-4">
              <div class="rounded p-3" style="background:#eff6ff; border:1px solid #bfdbfe;">
                <div class="d-flex align-items-center gap-2 mb-1">
                  <i class="fa-solid fa-warehouse text-primary"></i>
                  <span class="fw-semibold small">Inventory</span>
                </div>
                @if (isset($stats['inventory']['error']))
                  <span class="text-danger small">{{ $stats['inventory']['error'] }}</span>
                @else
                  <div class="small text-muted">
                    <span class="text-success fw-bold">{{ $stats['inventory']['updated'] }}</span> updated &nbsp;·&nbsp;
                    <span class="text-warning fw-bold">{{ $stats['inventory']['missed'] }}</span> not matched
                    @if (!empty($stats['inventory']['locations']))
                      &nbsp;·&nbsp; <span class="text-primary fw-bold">{{ $stats['inventory']['locations'] }}</span> location(s)
                    @endif
                  </div>
                @endif
              </div>
            </div>
          @endif

          {{-- Orders --}}
          @if (isset($stats['orders']))
            <div class="col-sm-4">
              <div class="rounded p-3" style="background:#fdf4ff; border:1px solid #e9d5ff;">
                <div class="d-flex align-items-center gap-2 mb-1">
                  <i class="fa-solid fa-bag-shopping" style="color:#9333ea;"></i>
                  <span class="fw-semibold small">Orders</span>
                </div>
                @if (isset($stats['orders']['error']))
                  <span class="text-danger small">{{ $stats['orders']['error'] }}</span>
                @else
                  <div class="small text-muted">
                    <span class="fw-bold" style="color:#9333ea;">{{ $stats['orders']['created'] }}</span> created &nbsp;·&nbsp;
                    <span class="text-warning fw-bold">{{ $stats['orders']['skipped'] }}</span> skipped
                    @if (!empty($stats['orders']['customers']))
                      &nbsp;·&nbsp; <span class="text-info fw-bold">{{ $stats['orders']['customers'] }}</span> customer(s) synced
                    @endif
                  </div>
                @endif
              </div>
            </div>
          @endif

        </div>

        {{-- Raw output (collapsible) --}}
        @if (session('import_output'))
          <details class="mt-1">
            <summary class="small text-muted" style="cursor:pointer; user-select:none;">
              <i class="fa-solid fa-terminal me-1"></i> Show raw output
            </summary>
            <pre class="mt-2 p-3 rounded small"
                 style="background:#1e1e2e; color:#cdd6f4; font-size:.75rem; white-space:pre-wrap; max-height:260px; overflow-y:auto;">{{ session('import_output') }}</pre>
          </details>
        @endif

      </div>
    </div>
  @endif

  {{-- ── Errors ──────────────────────────────────────────────── --}}
  @if ($errors->any())
    <div class="alert alert-danger py-2 mb-4">
      <ul class="mb-0 ps-3 small">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="row g-4">

    {{-- ── Upload form ─────────────────────────────────────── --}}
    <div class="col-xl-7 col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 px-4">
          <h6 class="mb-0 fw-semibold">
            <i class="fa-solid fa-upload text-primary me-2"></i>Upload CSV files
          </h6>
        </div>
        <div class="card-body p-4">

          <form method="POST" action="{{ route('admin.import.store') }}"
                enctype="multipart/form-data" id="importForm">
            @csrf

            {{-- Products --}}
            <div class="mb-4">
              <label class="form-label fw-semibold mb-1">
                <i class="fa-solid fa-box text-success me-1"></i> Products CSV
                <span class="badge text-bg-secondary fw-normal ms-1" style="font-size:.65rem;">optional</span>
              </label>
              <input type="file" name="products_csv" id="products_csv" accept=".csv,.txt"
                     class="form-control @error('products_csv') is-invalid @enderror"
                     onchange="updateFileName(this,'products_label')">
              @error('products_csv')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text" id="products_label">
                Export from: <strong>Products → All products → Export</strong> &nbsp;·&nbsp; file: <code>products_export.csv</code>
              </div>
            </div>

            {{-- Inventory --}}
            <div class="mb-4">
              <label class="form-label fw-semibold mb-1">
                <i class="fa-solid fa-warehouse text-primary me-1"></i> Inventory CSV
                <span class="badge text-bg-secondary fw-normal ms-1" style="font-size:.65rem;">optional</span>
              </label>
              <input type="file" name="inventory_csv" id="inventory_csv" accept=".csv,.txt"
                     class="form-control @error('inventory_csv') is-invalid @enderror"
                     onchange="updateFileName(this,'inventory_label')">
              @error('inventory_csv')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text" id="inventory_label">
                Export from: <strong>Products → Inventory → Export</strong> &nbsp;·&nbsp; file: <code>inventory_export.csv</code>
                <span class="ms-2 text-info"><i class="fa-solid fa-circle-info"></i> Locations are created automatically if missing</span>
              </div>
            </div>

            {{-- Orders --}}
            <div class="mb-4">
              <label class="form-label fw-semibold mb-1">
                <i class="fa-solid fa-bag-shopping me-1" style="color:#9333ea;"></i> Orders CSV
                <span class="badge text-bg-secondary fw-normal ms-1" style="font-size:.65rem;">optional</span>
              </label>
              <input type="file" name="orders_csv" id="orders_csv" accept=".csv,.txt"
                     class="form-control @error('orders_csv') is-invalid @enderror"
                     onchange="updateFileName(this,'orders_label')">
              @error('orders_csv')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text" id="orders_label">
                Export from: <strong>Orders → Export</strong> &nbsp;·&nbsp; file: <code>orders_export.csv</code>
              </div>
            </div>

            <hr class="my-4">

            {{-- Fresh option --}}
            <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" name="fresh" id="freshCheck" value="1">
              <label class="form-check-label" for="freshCheck">
                <span class="fw-semibold text-danger">Fresh import</span>
                <span class="d-block text-muted small mt-1">
                  <i class="fa-solid fa-triangle-exclamation text-danger me-1"></i>
                  Deletes all existing products, variants, images, inventory &amp; orders before importing.
                  Use only for a clean start.
                </span>
              </label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn btn-primary px-5" id="importBtn">
              <i class="fa-solid fa-upload me-2"></i> Start Import
            </button>

          </form>

        </div>
      </div>
    </div>

    {{-- ── Instructions ─────────────────────────────────────── --}}
    <div class="col-xl-5 col-lg-4">

      <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-bottom py-3 px-4">
          <h6 class="mb-0 fw-semibold">
            <i class="fa-brands fa-shopify text-success me-2"></i>How to export from Shopify
          </h6>
        </div>
        <div class="card-body p-4">
          <ol class="small text-muted ps-3 mb-0" style="line-height:2">
            <li>Log in to your <strong>Shopify Admin</strong>.</li>
            <li>
              <strong>Products CSV:</strong><br>
              Products → All products → <em>Export</em> → All products → CSV for spreadsheet apps
            </li>
            <li>
              <strong>Inventory CSV:</strong><br>
              Products → Inventory → <em>Export</em>
            </li>
            <li>
              <strong>Orders CSV:</strong><br>
              Orders → <em>Export</em> → All orders
            </li>
            <li>Upload the downloaded files above and click <strong>Start Import</strong>.</li>
          </ol>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 px-4">
          <h6 class="mb-0 fw-semibold">
            <i class="fa-solid fa-lightbulb text-warning me-2"></i>What happens during import
          </h6>
        </div>
        <div class="card-body p-4">
          <ul class="small text-muted ps-3 mb-0" style="line-height:2">
            <li>Files are processed in order: <strong>Products → Inventory → Orders</strong></li>
            <li>Duplicate products (same handle) are <strong>skipped</strong></li>
            <li>Inventory creates <strong>warehouse locations</strong> automatically if they don't exist</li>
            <li>Inventory updates <strong>stock quantities</strong> per variant per location</li>
            <li>Orders match line items by <strong>SKU</strong></li>
            <li>Duplicate orders (same order number) are <strong>skipped</strong></li>
            <li>Each file is <strong>optional</strong> — import only what you need</li>
          </ul>
        </div>
      </div>

    </div>
  </div>

</div>

{{-- Loading overlay --}}
<div id="importOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:9999;
     display:none; align-items:center; justify-content:center; flex-direction:column; gap:1rem;">
  <div class="spinner-border text-light" style="width:3rem;height:3rem;"></div>
  <p class="text-white fw-semibold mb-0">Importing data… please wait</p>
  <p class="text-white-50 small mb-0">This may take a minute for large catalogues. Do not close this tab.</p>
</div>

<script>
  document.getElementById('importForm').addEventListener('submit', function (e) {
    // Validate at least one file selected
    const hasFile = ['products_csv','inventory_csv','orders_csv']
      .some(n => document.getElementById(n).files.length > 0);

    if (!hasFile) {
      e.preventDefault();
      alert('Please select at least one CSV file to import.');
      return;
    }

    // Show overlay and disable button
    const overlay = document.getElementById('importOverlay');
    overlay.style.display = 'flex';

    const btn = document.getElementById('importBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Importing…';
  });

  function updateFileName(input, labelId) {
    const label = document.getElementById(labelId);
    if (input.files && input.files[0]) {
      const name = input.files[0].name;
      const size = (input.files[0].size / 1024).toFixed(1);
      label.innerHTML = `<i class="fa-solid fa-check text-success me-1"></i><strong>${name}</strong> <span class="text-muted">(${size} KB)</span>`;
    }
  }
</script>
@endsection
