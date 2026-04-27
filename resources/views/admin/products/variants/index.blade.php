@extends('admin.layouts.app')

@section('title', 'Variants')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <div class="h4 mb-0">Variants</div>
      <div class="text-secondary small">{{ $product->title }}</div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="{{ route('admin.products.edit', $product) }}">Back</a>
      <a class="btn btn-primary" href="{{ route('admin.products.variants.create', $product) }}">New variant</a>
    </div>
  </div>

  <div class="card shadow-sm border-0">
    <div class="table-responsive">
      <table class="table mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th>Title</th>
            <th>SKU</th>
            <th class="text-end">Price</th>
            <th class="text-end">Qty</th>
            <th>Active</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($product->variants as $variant)
            <tr>
              <td class="fw-semibold">{{ $variant->title }}</td>
              <td class="text-secondary">{{ $variant->sku }}</td>
              <td class="text-end">${{ number_format((float)$variant->price, 2) }}</td>
              <td class="text-end">{{ $variant->inventory_quantity }}</td>
              <td>
                @if($variant->is_active)
                  <span class="badge text-bg-success">Yes</span>
                @else
                  <span class="badge text-bg-secondary">No</span>
                @endif
              </td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.products.variants.edit', [$product, $variant]) }}">Edit</a>
                <form class="d-inline" method="POST" action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" onsubmit="return confirm('Delete this variant?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection

