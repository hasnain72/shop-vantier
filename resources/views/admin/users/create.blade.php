@extends('admin.layouts.app')
@section('title', 'New Admin User')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">New Admin User</div>
  <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Back
  </a>
</div>

<form method="POST" action="{{ route('admin.users.store') }}">
  @csrf
  @include('admin.users._form', ['user' => null, 'currentRole' => old('role', 'admin')])

  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Create user</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
  </div>
</form>
@endsection
