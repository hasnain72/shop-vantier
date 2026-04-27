@extends('admin.layouts.app')

@section('title', 'Edit Variant')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <div class="h4 mb-0">Edit variant</div>
      <div class="text-secondary small">{{ $product->title }}</div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="{{ route('admin.products.variants.index', $product) }}">Back</a>
      <form method="POST" action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" onsubmit="return confirm('Delete this variant?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger" type="submit">Delete</button>
      </form>
    </div>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.products.variants.update', [$product, $variant]) }}">
        @method('PUT')
        @include('admin.products.variants.form')
        <button class="btn btn-primary" type="submit">Save</button>
      </form>
    </div>
  </div>
@endsection

