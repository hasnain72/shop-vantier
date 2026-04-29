@if(isset($breadcrumbs) && count($breadcrumbs))
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb mb-0">
    @foreach($breadcrumbs as $i => $crumb)
      @if($i === count($breadcrumbs) - 1)
        <li class="breadcrumb-item active" aria-current="page">{{ $crumb['label'] }}</li>
      @else
        <li class="breadcrumb-item">
          @if(isset($crumb['url']))
            <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
          @else
            {{ $crumb['label'] }}
          @endif
        </li>
      @endif
    @endforeach
  </ol>
</nav>
@endif
