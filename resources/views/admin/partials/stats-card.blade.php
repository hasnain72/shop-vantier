{{--
  Usage: @include('admin.partials.stats-card', [
    'label'  => 'Total Orders',
    'value'  => '142',
    'icon'   => 'fa-bag-shopping',
    'color'  => 'primary',   // primary | success | warning | danger | info
    'change' => '+12%',      // optional
    'href'   => route('admin.orders.index'), // optional
  ])
--}}
@php $color = $color ?? 'primary'; @endphp
<div class="card border-0 shadow-sm h-100 {{ isset($href) ? 'card-hover' : '' }}">
  @isset($href)<a href="{{ $href }}" class="stretched-link text-decoration-none"></a>@endisset
  <div class="card-body d-flex align-items-center gap-3">
    <div class="rounded-3 p-3 bg-{{ $color }} bg-opacity-10">
      <i class="fa-solid {{ $icon ?? 'fa-chart-line' }} fa-lg text-{{ $color }}"></i>
    </div>
    <div>
      <div class="text-secondary small">{{ $label ?? 'Metric' }}</div>
      <div class="fw-bold fs-5 lh-1 mt-1">{{ $value ?? '—' }}</div>
      @isset($change)
        <div class="small mt-1 {{ str_starts_with($change, '+') ? 'text-success' : 'text-danger' }}">
          {{ $change }} vs last period
        </div>
      @endisset
    </div>
  </div>
</div>
