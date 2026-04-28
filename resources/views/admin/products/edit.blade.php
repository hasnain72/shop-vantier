@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div class="h4 mb-0">Edit product</div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="{{ route('admin.products.index') }}">Back</a>
      <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger" type="submit">Delete</button>
      </form>
    </div>
  </div>

  <div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.products.form')
        <button class="btn btn-primary" type="submit">Save</button>
      </form>
    </div>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="fw-semibold">Variants</div>
        <div class="d-flex gap-2">
          <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.products.variants.index', $product) }}">Manage variants</a>
          <a class="btn btn-sm btn-primary" href="{{ route('admin.products.variants.create', $product) }}">Add variant</a>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th>Title</th>
              <th>SKU</th>
              <th>Price</th>
              <th>Qty</th>
              <th>Active</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($product->variants as $variant)
              <tr>
                <td>{{ $variant->title }}</td>
                <td class="text-secondary">{{ $variant->sku }}</td>
                <td>${{ number_format((float)$variant->price, 2) }}</td>
                <td>{{ $variant->inventory_quantity }}</td>
                <td>
                  @if($variant->is_active)
                    <span class="badge text-bg-success">Yes</span>
                  @else
                    <span class="badge text-bg-secondary">No</span>
                  @endif
                </td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.products.variants.edit', [$product, $variant]) }}">Edit</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection

