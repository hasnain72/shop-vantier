@csrf

<div class="mb-3">
  <label class="form-label">Title</label>
  <input name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $collection->title ?? '') }}" required>
  @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label class="form-label">Description</label>
  <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $collection->description ?? '') }}</textarea>
  @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Sort order</label>
    <select name="sort_order" class="form-select @error('sort_order') is-invalid @enderror" required>
      @php($value = old('sort_order', $collection->sort_order ?? 'manual'))
      @foreach(['manual','best-selling','alpha-asc','alpha-desc','price-asc','price-desc','created-asc','created-desc'] as $opt)
        <option value="{{ $opt }}" @selected($value === $opt)>{{ $opt }}</option>
      @endforeach
    </select>
    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6">
    <label class="form-label">Template suffix</label>
    <input name="template_suffix" class="form-control @error('template_suffix') is-invalid @enderror" value="{{ old('template_suffix', $collection->template_suffix ?? '') }}">
    @error('template_suffix')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="row g-3 mt-0">
  <div class="col-md-6">
    <label class="form-label">Meta title</label>
    <input name="meta_title" class="form-control @error('meta_title') is-invalid @enderror" value="{{ old('meta_title', $collection->meta_title ?? '') }}">
    @error('meta_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6">
    <label class="form-label">Published at</label>
    <input type="datetime-local" name="published_at" class="form-control @error('published_at') is-invalid @enderror"
      value="{{ old('published_at', optional($collection->published_at ?? null)?->format('Y-m-d\\TH:i')) }}">
    @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="mb-3 mt-3">
  <label class="form-label">Meta description</label>
  <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="2">{{ old('meta_description', $collection->meta_description ?? '') }}</textarea>
  @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-check mb-3">
  <input class="form-check-input" type="checkbox" value="1" id="published" name="published" @checked(old('published', $collection->published ?? true))>
  <label class="form-check-label" for="published">
    Published
  </label>
</div>

