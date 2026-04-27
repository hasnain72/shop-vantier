@csrf

<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Title</label>
    <input name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $variant->title ?? '') }}" required>
    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6">
    <label class="form-label">SKU</label>
    <input name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $variant->sku ?? '') }}">
    @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="row g-3 mt-0">
  <div class="col-md-6">
    <label class="form-label">Price</label>
    <input name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $variant->price ?? '0.00') }}" required>
    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6">
    <label class="form-label">Compare at price</label>
    <input name="compare_at_price" class="form-control @error('compare_at_price') is-invalid @enderror" value="{{ old('compare_at_price', $variant->compare_at_price ?? '') }}">
    @error('compare_at_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="row g-3 mt-0">
  <div class="col-md-6">
    <label class="form-label">Cost per item</label>
    <input name="cost_per_item" class="form-control @error('cost_per_item') is-invalid @enderror" value="{{ old('cost_per_item', $variant->cost_per_item ?? '') }}">
    @error('cost_per_item')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6">
    <label class="form-label">Barcode</label>
    <input name="barcode" class="form-control @error('barcode') is-invalid @enderror" value="{{ old('barcode', $variant->barcode ?? '') }}">
    @error('barcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="row g-3 mt-0">
  <div class="col-md-4">
    <label class="form-label">Option 1</label>
    <input name="option1" class="form-control" value="{{ old('option1', $variant->option1 ?? '') }}">
  </div>
  <div class="col-md-4">
    <label class="form-label">Option 2</label>
    <input name="option2" class="form-control" value="{{ old('option2', $variant->option2 ?? '') }}">
  </div>
  <div class="col-md-4">
    <label class="form-label">Option 3</label>
    <input name="option3" class="form-control" value="{{ old('option3', $variant->option3 ?? '') }}">
  </div>
</div>

<div class="row g-3 mt-0">
  <div class="col-md-6">
    <label class="form-label">Inventory quantity</label>
    <input name="inventory_quantity" class="form-control @error('inventory_quantity') is-invalid @enderror" value="{{ old('inventory_quantity', $variant->inventory_quantity ?? 0) }}">
    @error('inventory_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-3">
    <label class="form-label">Weight</label>
    <input name="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight', $variant->weight ?? '') }}">
    @error('weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-3">
    <label class="form-label">Unit</label>
    @php($wu = old('weight_unit', $variant->weight_unit ?? 'kg'))
    <select name="weight_unit" class="form-select @error('weight_unit') is-invalid @enderror" required>
      @foreach(['kg','g','lb','oz'] as $opt)
        <option value="{{ $opt }}" @selected($wu === $opt)>{{ $opt }}</option>
      @endforeach
    </select>
    @error('weight_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="row g-3 mt-0">
  <div class="col-md-4">
    <div class="form-check mt-4">
      <input class="form-check-input" type="checkbox" value="1" id="requires_shipping" name="requires_shipping"
        @checked(old('requires_shipping', $variant->requires_shipping ?? true))>
      <label class="form-check-label" for="requires_shipping">Requires shipping</label>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-check mt-4">
      <input class="form-check-input" type="checkbox" value="1" id="taxable" name="taxable"
        @checked(old('taxable', $variant->taxable ?? true))>
      <label class="form-check-label" for="taxable">Taxable</label>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-check mt-4">
      <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active"
        @checked(old('is_active', $variant->is_active ?? true))>
      <label class="form-check-label" for="is_active">Active</label>
    </div>
  </div>
</div>

