@extends('admin.layouts.app')
@section('title', 'Activity Log')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h4 mb-0 fw-bold">Activity Log</h1>
    <p class="text-muted small mb-0">Track all admin actions across the store.</p>
  </div>
</div>

{{-- Filters --}}
<form method="GET" class="row g-2 mb-4 align-items-end">
  <div class="col-md-4">
    <input name="search" type="text" class="form-control form-control-sm"
      placeholder="Search description…" value="{{ request('search') }}">
  </div>
  <div class="col-md-3">
    <select name="event" class="form-select form-select-sm">
      <option value="">All events</option>
      @foreach($events as $event)
        <option value="{{ $event }}" @selected(request('event') === $event)>{{ $event }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <input name="subject_type" class="form-control form-control-sm"
      placeholder="Filter by type (e.g. Order)" value="{{ request('subject_type') }}">
  </div>
  <div class="col-md-2">
    <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
  </div>
</form>

<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    @if($logs->isEmpty())
      <div class="text-center py-5 text-muted">
        <i class="fas fa-history fa-2x mb-3 d-block opacity-25"></i>
        No activity recorded yet.
      </div>
    @else
    <div class="table-responsive">
      <table class="table table-hover table-sm mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:160px;">Date</th>
            <th>User</th>
            <th>Event</th>
            <th>Description</th>
            <th>Subject</th>
            <th>IP</th>
          </tr>
        </thead>
        <tbody>
          @foreach($logs as $log)
          <tr>
            <td class="small text-muted text-nowrap">{{ $log->created_at->format('M d, Y H:i') }}</td>
            <td class="small">{{ $log->user->name ?? '<em>System</em>' }}</td>
            <td>
              @php
                $badge = match($log->event) {
                    'created'  => 'bg-success-subtle text-success',
                    'updated'  => 'bg-primary-subtle text-primary',
                    'deleted'  => 'bg-danger-subtle text-danger',
                    'login'    => 'bg-info-subtle text-info',
                    default    => 'bg-secondary-subtle text-secondary',
                };
              @endphp
              <span class="badge {{ $badge }}">{{ $log->event ?? '—' }}</span>
            </td>
            <td class="small">{{ $log->description }}</td>
            <td class="small text-muted">
              @if($log->subject_type)
                {{ class_basename($log->subject_type) }}
                @if($log->subject_id)#{{ $log->subject_id }}@endif
              @else
                —
              @endif
            </td>
            <td class="small font-monospace text-muted">{{ $log->ip_address ?? '—' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top">
      <small class="text-muted">{{ $logs->total() }} total records</small>
      {{ $logs->links() }}
    </div>
    @endif
  </div>
</div>
@endsection
