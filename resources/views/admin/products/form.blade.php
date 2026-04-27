@csrf

<div class="mb-3">
  <label class="form-label">Title</label>
  <input name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $product->title ?? '') }}" required>
  @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Status</label>
    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
      @php($value = old('status', $product->status ?? 'draft'))
      @foreach(['draft','active','archived'] as $opt)
        <option value="{{ $opt }}" @selected($value === $opt)>{{ $opt }}</option>
      @endforeach
    </select>
    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6">
    <label class="form-label">Product type</label>
    <select name="product_type_id" class="form-select @error('product_type_id') is-invalid @enderror">
      <option value="">—</option>
      @php($pt = old('product_type_id', $product->product_type_id ?? null))
      @foreach($productTypes as $type)
        <option value="{{ $type->id }}" @selected((string)$pt === (string)$type->id)>{{ $type->name }}</option>
      @endforeach
    </select>
    @error('product_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="row g-3 mt-0">
  <div class="col-md-6">
    <label class="form-label">Vendor</label>
    <input name="vendor" class="form-control @error('vendor') is-invalid @enderror" value="{{ old('vendor', $product->vendor ?? '') }}">
    @error('vendor')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6">
    <label class="form-label">Published at</label>
    <input type="datetime-local" name="published_at" class="form-control @error('published_at') is-invalid @enderror"
      value="{{ old('published_at', optional($product->published_at ?? null)?->format('Y-m-d\\TH:i')) }}">
    @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="mb-3 mt-3">
  <label class="form-label">Tags (comma separated)</label>
  <input name="tags" class="form-control @error('tags') is-invalid @enderror"
    value="{{ old('tags', isset($product) ? implode(', ', $product->tags ?? []) : '') }}">
  @error('tags')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label class="form-label">Description (HTML)</label>
  <textarea name="body_html" class="form-control @error('body_html') is-invalid @enderror" rows="6">{{ old('body_html', $product->body_html ?? '') }}</textarea>
  @error('body_html')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Meta title</label>
    <input name="meta_title" class="form-control @error('meta_title') is-invalid @enderror" value="{{ old('meta_title', $product->meta_title ?? '') }}">
    @error('meta_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6">
    <label class="form-label">Meta description</label>
    <input name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" value="{{ old('meta_description', $product->meta_description ?? '') }}">
    @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="row g-3 mt-0">
  <div class="col-md-6">
    <div class="form-check mt-4">
      <input class="form-check-input" type="checkbox" value="1" id="requires_shipping" name="requires_shipping"
        @checked(old('requires_shipping', $product->requires_shipping ?? true))>
      <label class="form-check-label" for="requires_shipping">Requires shipping</label>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-check mt-4">
      <input class="form-check-input" type="checkbox" value="1" id="taxable" name="taxable"
        @checked(old('taxable', $product->taxable ?? true))>
      <label class="form-check-label" for="taxable">Taxable</label>
    </div>
  </div>
</div>

<div class="mb-3 mt-3">
  <label class="form-label">Collections</label>
  @php($selected = old('collection_ids', isset($product) ? $product->collections->pluck('id')->all() : []))
  <select name="collection_ids[]" class="form-select" multiple size="6">
    @foreach($collections as $collection)
      <option value="{{ $collection->id }}" @selected(in_array($collection->id, $selected))>{{ $collection->title }}</option>
    @endforeach
  </select>
</div>

@if(!isset($product))
  <hr class="my-4">
  <div class="h6 mb-2">Default variant</div>
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Price</label>
      <input name="default_price" class="form-control @error('default_price') is-invalid @enderror" value="{{ old('default_price', '0.00') }}" required>
      @error('default_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
      <label class="form-label">SKU</label>
      <input name="default_sku" class="form-control @error('default_sku') is-invalid @enderror" value="{{ old('default_sku', '') }}">
      @error('default_sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
@endif

