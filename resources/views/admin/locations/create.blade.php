@extends('admin.layouts.app')
@section('title', 'New Location')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">New location</div>
  <a href="{{ route('admin.locations.index') }}" class="btn btn-outline-secondary">Back</a>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px;">
  <div class="card-body">
    <form method="POST" action="{{ route('admin.locations.store') }}">
      @csrf
      @include('admin.locations.form')
      <button class="btn btn-primary mt-3" type="submit">Create location</button>
    </form>
  </div>
</div>
@endsection
