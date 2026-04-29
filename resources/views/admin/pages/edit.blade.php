@extends('admin.layouts.app')
@section('title', 'Edit Page')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Edit page</div>
  <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">Back</a>
</div>
<form method="POST" action="{{ route('admin.pages.update', $page) }}">
  @method('PUT')
  @include('admin.pages.form')
  <div class="mt-3">
    <button type="submit" class="btn btn-primary">Save changes</button>
  </div>
</form>
@endsection
