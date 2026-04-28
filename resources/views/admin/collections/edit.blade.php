@extends('admin.layouts.app')

@section('title', 'Edit Collection')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div class="h4 mb-0">Edit collection</div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="{{ route('admin.collections.index') }}">Back</a>
      <form method="POST" action="{{ route('admin.collections.destroy', $collection) }}" onsubmit="return confirm('Delete this collection?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger" type="submit">Delete</button>
      </form>
    </div>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.collections.update', $collection) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.collections.form')
        <button class="btn btn-primary" type="submit">Save</button>
      </form>
    </div>
  </div>
@endsection

