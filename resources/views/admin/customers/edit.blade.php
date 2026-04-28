@extends('admin.layouts.app')
@section('title', 'Edit Customer')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Edit customer</div>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="{{ route('admin.customers.show', $customer) }}">Back</a>
    <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}"
      onsubmit="return confirm('Delete this customer?')">
      @csrf @method('DELETE')
      <button class="btn btn-outline-danger">Delete</button>
    </form>
  </div>
</div>

<form method="POST" action="{{ route('admin.customers.update', $customer) }}">
  @method('PUT')
  @include('admin.customers.form')
  <div class="mt-3">
    <button class="btn btn-primary" type="submit">Save changes</button>
  </div>
</form>
@endsection
