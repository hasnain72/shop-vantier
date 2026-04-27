@extends('admin.layouts.app')

@section('title', 'New Collection')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div class="h4 mb-0">New collection</div>
    <a class="btn btn-outline-secondary" href="{{ route('admin.collections.index') }}">Back</a>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.collections.store') }}">
        @include('admin.collections.form')
        <button class="btn btn-primary" type="submit">Create</button>
      </form>
    </div>
  </div>
@endsection

