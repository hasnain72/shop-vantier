@extends('admin.layouts.app')
@section('title', 'New Article')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">New article — {{ $blog->title }}</div>
  <a href="{{ route('admin.blogs.articles', $blog) }}" class="btn btn-outline-secondary">Back</a>
</div>
<form method="POST" action="{{ route('admin.blogs.articles.store', $blog) }}">
  @include('admin.articles.form')
  <div class="mt-3">
    <button type="submit" class="btn btn-primary">Publish article</button>
  </div>
</form>
@endsection
