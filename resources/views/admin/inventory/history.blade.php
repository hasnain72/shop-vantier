@extends('admin.layouts.app')
@section('title', 'Inventory History')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <div class="h4 mb-0">Inventory history</div>
    <div class="text-secondary small">{{ $item->variant?->product?->title }} — {{ $item->variant?->title }}</div>
  </div>
  <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead class="table-light">
        <tr>
          <th>Date</th>
          <th>Location</th>
          <th>Reason</th>
          <th>Adjustment</th>
          <th>Before</th>
          <th>After</th>
          <th>Staff</th>
        </tr>
      </thead>
      <tbody>
        @forelse($history as $adj)
          <tr>
            <td class="text-secondary small">{{ $adj->created_at->format('M j, Y H:i') }}</td>
            <td class="small">{{ $adj->location?->name ?? '—' }}</td>
            <td><span class="badge text-bg-secondary">{{ $adj->reason }}</span></td>
            <td>
              <span class="fw-semibold {{ $adj->adjustment > 0 ? 'text-success' : 'text-danger' }}">
                {{ $adj->adjustment > 0 ? '+' : '' }}{{ $adj->adjustment }}
              </span>
            </td>
            <td class="text-secondary small">{{ $adj->available_before }}</td>
            <td class="text-secondary small">{{ $adj->available_after }}</td>
            <td class="text-secondary small">{{ $adj->user?->name ?? 'System' }}</td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center text-secondary py-4">No history found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
