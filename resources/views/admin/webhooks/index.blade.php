@extends('admin.layouts.app')

@section('title', 'Webhooks')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h4 mb-0 fw-bold">Webhooks</h1>
    <p class="text-muted small mb-0">Receive real-time HTTP notifications for store events.</p>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
    <i class="fas fa-plus me-2"></i>Add Webhook
  </button>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- Webhooks table --}}
<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    @if($webhooks->isEmpty())
      <div class="text-center py-5 text-muted">
        <i class="fas fa-plug fa-2x mb-3 d-block opacity-25"></i>
        No webhooks configured yet.
      </div>
    @else
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>Topic</th>
            <th>Address</th>
            <th>Status</th>
            <th>Last triggered</th>
            <th>Failures</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($webhooks as $webhook)
          <tr>
            <td>
              <code class="small">{{ $webhook->topic }}</code>
            </td>
            <td class="small text-truncate" style="max-width:240px;" title="{{ $webhook->address }}">
              {{ $webhook->address }}
            </td>
            <td>
              @if($webhook->is_active)
                <span class="badge bg-success-subtle text-success">Active</span>
              @else
                <span class="badge bg-danger-subtle text-danger">Disabled</span>
              @endif
            </td>
            <td class="small text-muted">
              {{ $webhook->last_triggered_at ? $webhook->last_triggered_at->diffForHumans() : '—' }}
              @if($webhook->last_status_code)
                <span class="badge {{ $webhook->last_status_code < 300 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} ms-1">
                  {{ $webhook->last_status_code }}
                </span>
              @endif
            </td>
            <td>
              @if($webhook->failure_count > 0)
                <span class="badge bg-warning-subtle text-warning">{{ $webhook->failure_count }}</span>
              @else
                <span class="text-muted small">0</span>
              @endif
            </td>
            <td class="text-end">
              <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary" title="Edit"
                  data-bs-toggle="modal" data-bs-target="#editModal{{ $webhook->id }}">
                  <i class="fas fa-edit"></i>
                </button>
                <form action="{{ route('admin.webhooks.secret', $webhook) }}" method="POST" class="d-inline">
                  @csrf @method('PATCH')
                  <button type="submit" class="btn btn-outline-warning" title="Regenerate secret"
                    onclick="return confirm('Regenerate the signing secret? Existing integrations will need to be updated.')">
                    <i class="fas fa-key"></i>
                  </button>
                </form>
                <form action="{{ route('admin.webhooks.destroy', $webhook) }}" method="POST" class="d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger" title="Delete"
                    onclick="return confirm('Delete this webhook?')">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>

          {{-- Edit modal for each webhook --}}
          <div class="modal fade" id="editModal{{ $webhook->id }}" tabindex="-1">
            <div class="modal-dialog">
              <form action="{{ route('admin.webhooks.update', $webhook) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Webhook</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label fw-semibold">Topic</label>
                      <select name="topic" class="form-select" required>
                        @foreach($topics as $t)
                          <option value="{{ $t }}" {{ $webhook->topic === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-semibold">Endpoint URL</label>
                      <input type="url" name="address" class="form-control" value="{{ $webhook->address }}" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-semibold">Secret (read-only)</label>
                      <div class="input-group">
                        <input type="text" class="form-control font-monospace small" value="{{ $webhook->secret }}" readonly>
                        <button type="button" class="btn btn-outline-secondary"
                          onclick="navigator.clipboard.writeText('{{ $webhook->secret }}')">
                          <i class="fas fa-copy"></i>
                        </button>
                      </div>
                      <div class="form-text">Used to verify the <code>X-Webhook-Hmac-Sha256</code> header.</div>
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" name="is_active" value="1"
                        {{ $webhook->is_active ? 'checked' : '' }} id="activeSwitch{{ $webhook->id }}">
                      <label class="form-check-label" for="activeSwitch{{ $webhook->id }}">Active</label>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>
</div>

{{-- Info card --}}
<div class="card border-0 shadow-sm mt-4">
  <div class="card-body">
    <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2 text-info"></i>Verifying Webhook Signatures</h6>
    <p class="small text-muted mb-2">
      Each request includes an <code>X-Webhook-Hmac-Sha256</code> header.
      Compute <code>base64(HMAC-SHA256(secret, raw_body))</code> and compare to verify authenticity.
    </p>
    <pre class="bg-light rounded p-3 small mb-0"><code>$computed = base64_encode(hash_hmac('sha256', $rawBody, $secret, true));
$isValid  = hash_equals($computed, $request->header('X-Webhook-Hmac-Sha256'));</code></pre>
  </div>
</div>

{{-- Create modal --}}
<div class="modal fade" id="createModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('admin.webhooks.store') }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Webhook</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Topic <span class="text-danger">*</span></label>
            <select name="topic" class="form-select" required>
              <option value="">Select a topic…</option>
              @foreach($topics as $t)
                <option value="{{ $t }}">{{ $t }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Endpoint URL <span class="text-danger">*</span></label>
            <input type="url" name="address" class="form-control" placeholder="https://your-app.com/webhooks" required>
            <div class="form-text">Must be an HTTPS URL that accepts POST requests.</div>
          </div>
          <div class="alert alert-info small mb-0">
            <i class="fas fa-lock me-1"></i>
            A signing secret will be generated automatically.
            Use it to verify incoming webhook payloads.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Webhook</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
