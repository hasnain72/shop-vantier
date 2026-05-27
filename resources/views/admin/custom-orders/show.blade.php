@extends('admin.layouts.app')
@section('title', 'Custom Order #' . $customOrder->id)

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
  <a href="{{ route('admin.custom-orders.index') }}" class="btn btn-sm btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i>Back
  </a>
  <div class="h4 mb-0 ms-2">Custom Order #{{ $customOrder->id }}</div>
  <span class="{{ $customOrder->statusBadgeClass() }}">{{ ucfirst($customOrder->status) }}</span>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="row g-3">
  {{-- Contact details --}}
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-header bg-transparent fw-semibold">Contact Details</div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-sm-3">Name</dt>
          <dd class="col-sm-9">{{ $customOrder->name }}</dd>

          <dt class="col-sm-3">Email</dt>
          <dd class="col-sm-9">
            <a href="mailto:{{ $customOrder->email }}">{{ $customOrder->email }}</a>
          </dd>

          <dt class="col-sm-3">Phone</dt>
          <dd class="col-sm-9">
            @if($customOrder->phone)
              <a href="tel:{{ $customOrder->phone }}">{{ $customOrder->phone }}</a>
            @else
              <span class="text-secondary">—</span>
            @endif
          </dd>

          <dt class="col-sm-3">Received</dt>
          <dd class="col-sm-9 text-secondary">
            {{ $customOrder->created_at->format('d M Y \a\t H:i') }}
            ({{ $customOrder->created_at->diffForHumans() }})
          </dd>
        </dl>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent fw-semibold">Message</div>
      <div class="card-body">
        <p class="mb-0" style="white-space: pre-wrap;">{{ $customOrder->message }}</p>
      </div>
    </div>
  </div>

  {{-- Update status & notes --}}
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent fw-semibold">Update Status & Notes</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.custom-orders.update', $customOrder) }}">
          @csrf @method('PUT')

          <div class="mb-3">
            <label class="form-label small fw-semibold">Status</label>
            <select name="status" class="form-select">
              @foreach(\App\Models\CustomOrder::STATUSES as $s)
                <option value="{{ $s }}" @selected($customOrder->status === $s)>
                  {{ ucfirst($s) }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Admin Notes</label>
            <textarea name="admin_notes" class="form-control" rows="5"
              placeholder="Internal notes, follow-up actions…">{{ old('admin_notes', $customOrder->admin_notes) }}</textarea>
          </div>

          <button type="submit" class="btn btn-primary w-100">Save Changes</button>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm mt-3">
      <div class="card-header bg-transparent fw-semibold">Quick Actions</div>
      <div class="card-body d-flex flex-column gap-2">
        <a href="mailto:{{ $customOrder->email }}?subject=Re: Your Custom Strap Request"
           class="btn btn-outline-primary">
          <i class="bi bi-envelope me-1"></i>Reply by Email
        </a>
        @if($customOrder->phone)
          <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customOrder->phone) }}"
             target="_blank" rel="noopener noreferrer" class="btn btn-outline-success">
            <i class="bi bi-whatsapp me-1"></i>Open in WhatsApp
          </a>
        @endif
        <form method="POST" action="{{ route('admin.custom-orders.destroy', $customOrder) }}"
              onsubmit="return confirm('Delete this enquiry permanently?')">
          @csrf @method('DELETE')
          <button class="btn btn-outline-danger w-100">
            <i class="bi bi-trash me-1"></i>Delete
          </button>
        </form>
      </div>
    </div>

    {{-- Meta --}}
    <div class="card border-0 shadow-sm mt-3">
      <div class="card-header bg-transparent fw-semibold">Technical Details</div>
      <div class="card-body">
        <dl class="row mb-0 small text-secondary">
          <dt class="col-5">IP Address</dt>
          <dd class="col-7 text-truncate">{{ $customOrder->ip_address ?? '—' }}</dd>
          <dt class="col-5">User Agent</dt>
          <dd class="col-7 text-truncate" title="{{ $customOrder->user_agent }}">
            {{ Str::limit($customOrder->user_agent, 40) ?? '—' }}
          </dd>
        </dl>
      </div>
    </div>
  </div>
</div>
@endsection
