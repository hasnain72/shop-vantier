@extends('admin.layouts.app')
@section('title', 'Queue')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <div class="h4 mb-0">Background jobs</div>
    <div class="text-secondary small">Driver: <code>{{ $driver }}</code></div>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-3 p-3 bg-primary bg-opacity-10">
          <i class="fa-solid fa-clock-rotate-left fa-lg text-primary"></i>
        </div>
        <div class="flex-grow-1">
          <div class="text-secondary small">Pending jobs</div>
          <div class="fw-bold fs-4" id="pendingCount">{{ $pending }}</div>
        </div>
        <button class="btn btn-primary" id="runBtn" @disabled($pending === 0)>
          <i class="bi bi-play-fill me-1"></i>Run queue now
        </button>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-3 p-3 bg-danger bg-opacity-10">
          <i class="fa-solid fa-triangle-exclamation fa-lg text-danger"></i>
        </div>
        <div class="flex-grow-1">
          <div class="text-secondary small">Failed jobs</div>
          <div class="fw-bold fs-4" id="failedCount">{{ $failed }}</div>
        </div>
        <div class="d-flex flex-column gap-1">
          <form method="POST" action="{{ route('admin.queue.retry-failed') }}">
            @csrf
            <button class="btn btn-sm btn-outline-warning w-100" @disabled($failed === 0)>Retry all</button>
          </form>
          <form method="POST" action="{{ route('admin.queue.flush-failed') }}"
            onsubmit="return confirm('Delete all failed jobs?');">
            @csrf
            <button class="btn btn-sm btn-outline-danger w-100" @disabled($failed === 0)>Delete all</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div class="fw-semibold mb-2">About</div>
    <p class="text-secondary small mb-2">
      Background jobs run tasks like sending emails, webhooks, and low-stock alerts without slowing down
      customer requests. <strong>"Run queue now"</strong> processes all pending jobs in one go — useful
      during development, or if you don't have a persistent <code>queue:work</code> worker running.
    </p>
    <p class="text-secondary small mb-0">
      For production, run a long-lived worker instead:
      <code>php artisan queue:work --tries=3</code>
    </p>
    <div id="runOutput" class="alert alert-info small mt-3 d-none" style="white-space:pre-wrap;font-family:monospace;"></div>
    <div id="runError" class="alert alert-danger small mt-3 d-none"></div>
  </div>
</div>

@push('scripts')
<script>
  const runBtn = document.getElementById('runBtn');
  runBtn?.addEventListener('click', async function () {
    const originalHtml = runBtn.innerHTML;
    runBtn.disabled = true;
    runBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Working…';

    const outBox = document.getElementById('runOutput');
    const errBox = document.getElementById('runError');
    outBox.classList.add('d-none');
    errBox.classList.add('d-none');

    try {
      const res = await fetch('{{ route('admin.queue.run') }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
      });
      const data = await res.json();
      if (!res.ok || !data.success) throw new Error(data.message || 'Queue run failed.');

      document.getElementById('pendingCount').textContent = data.pending;
      document.getElementById('failedCount').textContent  = data.failed;

      outBox.textContent = `Processed ${data.processed} job(s). ${data.pending} pending, ${data.failed} failed.` +
        (data.output ? '\n\n' + data.output : '');
      outBox.classList.remove('d-none');
    } catch (err) {
      errBox.textContent = err.message;
      errBox.classList.remove('d-none');
    } finally {
      runBtn.disabled = false;
      runBtn.innerHTML = originalHtml;
    }
  });
</script>
@endpush
@endsection
