@extends('admin.layouts.app')
@section('title', 'Add Page')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Add page</div>
  <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">Back</a>
</div>
<form method="POST" action="{{ route('admin.pages.store') }}">
  @include('admin.pages.form')
  <div class="mt-3">
    <button type="submit" class="btn btn-primary">Save page</button>
  </div>
</form>
@endsection
