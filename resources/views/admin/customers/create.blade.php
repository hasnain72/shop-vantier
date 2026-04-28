@extends('admin.layouts.app')
@section('title', 'New Customer')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">New customer</div>
  <a class="btn btn-outline-secondary" href="{{ route('admin.customers.index') }}">Back</a>
</div>

<form method="POST" action="{{ route('admin.customers.store') }}">
  @include('admin.customers.form', ['customer' => null])
  <div class="mt-3">
    <button class="btn btn-primary" type="submit">Create customer</button>
  </div>
</form>
@endsection
