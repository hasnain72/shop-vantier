@extends('admin.layouts.app')
@section('title', 'Edit Admin User')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Edit Admin User</div>
  <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Back
  </a>
</div>

@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('admin.users.update', $user) }}">
  @csrf @method('PUT')
  @include('admin.users._form', [
    'user' => $user,
    'currentRole' => old('role', optional($user->roles->first())->name ?? 'admin'),
  ])

  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save changes</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
  </div>
</form>
@endsection
