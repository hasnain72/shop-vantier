@extends('admin.layouts.app')
@section('title', 'Create Discount')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Create discount</div>
  <a href="{{ route('admin.discounts.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<form method="POST" action="{{ route('admin.discounts.store') }}">
  @include('admin.discounts.form')
  <div class="mt-3">
    <button type="submit" class="btn btn-primary">Save discount</button>
    <a href="{{ route('admin.discounts.index') }}" class="btn btn-outline-secondary ms-2">Discard</a>
  </div>
</form>
@endsection
