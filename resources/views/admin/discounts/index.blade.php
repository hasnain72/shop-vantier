@extends('admin.layouts.app')
@section('title', 'Discounts')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Discounts</div>
  <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Create discount
  </a>
</div>

{{-- Filter tabs --}}
<ul class="nav nav-tabs mb-3">
  @foreach(['all' => 'All', 'active' => 'Active', 'scheduled' => 'Scheduled', 'expired' => 'Expired'] as $key => $label)
    <li class="nav-item">
      <a href="{{ request()->fullUrlWithQuery(['filter' => $key]) }}"
        class="nav-link {{ $filter === $key ? 'active' : '' }}">{{ $label }}</a>
    </li>
  @endforeach
</ul>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
      <thead class="table-light">
        <tr>
          <th>Code(s)</th>
          <th>Type</th>
          <th>Value</th>
          <th>Used / Limit</th>
          <th>Active dates</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($priceRules as $rule)
          <tr>
            <td>
              <div class="fw-semibold">{{ $rule->title }}</div>
              <div class="d-flex flex-wrap gap-1 mt-1">
                @foreach($rule->discountCodes->take(3) as $dc)
                  <code class="bg-light px-2 py-0 rounded small">{{ $dc->code }}</code>
                @endforeach
                @if($rule->discountCodes->count() > 3)
                  <span class="text-secondary small">+{{ $rule->discountCodes->count() - 3 }} more</span>
                @endif
              </div>
            </td>
            <td class="small text-secondary">
              {{ match($rule->value_type) {
                'percentage'   => 'Percentage',
                'fixed_amount' => 'Fixed amount',
                'free_shipping'=> 'Free shipping',
                'buy_x_get_y' => 'Buy X Get Y',
                default        => $rule->value_type,
              } }}
            </td>
            <td class="fw-semibold">
              @if($rule->value_type === 'percentage')
                {{ abs($rule->value) }}% off
              @elseif($rule->value_type === 'fixed_amount')
                ${{ number_format(abs($rule->value), 2) }} off
              @elseif($rule->value_type === 'free_shipping')
                Free shipping
              @endif
            </td>
            <td class="small">
              {{ $rule->usage_count }}
              @if($rule->usage_limit) / {{ $rule->usage_limit }} @else / ∞ @endif
            </td>
            <td class="small text-secondary">
              <div>{{ $rule->starts_at?->format('M j, Y') ?? '—' }}</div>
              @if($rule->ends_at)
                <div>→ {{ $rule->ends_at->format('M j, Y') }}</div>
              @else
                <div class="text-muted">No end date</div>
              @endif
            </td>
            <td>
              @php
                $now = now();
                if ($rule->starts_at && $rule->starts_at->isFuture()) {
                  $badge = ['Scheduled', 'warning'];
                } elseif ($rule->ends_at && $rule->ends_at->isPast()) {
                  $badge = ['Expired', 'secondary'];
                } elseif ($rule->usage_limit && $rule->usage_count >= $rule->usage_limit) {
                  $badge = ['Exhausted', 'danger'];
                } else {
                  $badge = ['Active', 'success'];
                }
              @endphp
              <span class="badge text-bg-{{ $badge[1] }}">{{ $badge[0] }}</span>
            </td>
            <td class="pe-3">
              <div class="d-flex gap-1 justify-content-end">
                <a href="{{ route('admin.discounts.edit', $rule) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                <form method="POST" action="{{ route('admin.discounts.destroy', $rule) }}"
                  onsubmit="return confirm('Delete this discount?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-secondary py-5">No discounts found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($priceRules->hasPages())
    <div class="card-footer bg-transparent">{{ $priceRules->links() }}</div>
  @endif
</div>
@endsection
