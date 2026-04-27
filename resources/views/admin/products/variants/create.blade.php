@extends('admin.layouts.app')

@section('title', 'New Variant')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <div class="h4 mb-0">New variant</div>
      <div class="text-secondary small">{{ $product->title }}</div>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('admin.products.variants.index', $product) }}">Back</a>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.products.variants.store', $product) }}">
        @include('admin.products.variants.form')
        <button class="btn btn-primary" type="submit">Create</button>
      </form>
    </div>
  </div>
@endsection

