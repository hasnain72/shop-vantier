@extends('admin.layouts.app')

@section('title', 'Products')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <div class="h4 mb-0">Products</div>
      <div class="text-secondary small">Manage products and variants.</div>
    </div>
    <a class="btn btn-primary" href="{{ route('admin.products.create') }}">New product</a>
  </div>

  <div class="card shadow-sm border-0">
    <div class="table-responsive">
      <table class="table mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th>Title</th>
            <th>Status</th>
            <th>Slug</th>
            <th>Variants</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($products as $product)
            <tr>
              <td class="fw-semibold">{{ $product->title }}</td>
              <td>
                <span class="badge text-bg-{{ $product->status === 'active' ? 'success' : ($product->status === 'draft' ? 'secondary' : 'warning') }}">
                  {{ $product->status }}
                </span>
              </td>
              <td class="text-secondary">{{ $product->slug }}</td>
              <td>{{ $product->variants_count }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                <form class="d-inline" method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-secondary py-4">No products yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">
    {{ $products->links() }}
  </div>
@endsection

