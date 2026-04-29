@extends('admin.layouts.app')
@section('title', 'Legal Pages')

@section('content')
<div class="h4 mb-1">Legal</div>
<p class="text-muted small mb-4">Your store's legal policies. These are served to customers at checkout.</p>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<form method="POST" action="{{ route('admin.settings.legal.update') }}">
  @csrf @method('PUT')

  @foreach($labels as $key => $label)
  @php $inputKey = str_replace('.', '_', $key); @endphp
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
      <span class="fw-semibold">{{ $label }}</span>
      <span class="badge bg-secondary-subtle text-muted small font-monospace">{{ $key }}</span>
    </div>
    <div class="card-body">
      <textarea name="{{ $inputKey }}" rows="8" class="form-control"
        placeholder="Enter your {{ strtolower($label) }} here…">{{ old($inputKey, $policies[$key] ?? '') }}</textarea>
      <div class="form-text">Markdown is supported.</div>
    </div>
  </div>
  @endforeach

  <button type="submit" class="btn btn-primary">Save policies</button>
</form>
@endsection
