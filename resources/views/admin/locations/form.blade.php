@csrf
<div class="mb-3">
  <label class="form-label">Location name <span class="text-danger">*</span></label>
  <input name="name" class="form-control @error('name') is-invalid @enderror"
    value="{{ old('name', $location->name ?? '') }}" required>
  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label">Address</label>
  <input name="address1" class="form-control" value="{{ old('address1', $location->address1 ?? '') }}">
</div>
<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">City</label>
    <input name="city" class="form-control" value="{{ old('city', $location->city ?? '') }}">
  </div>
  <div class="col-md-4">
    <label class="form-label">ZIP</label>
    <input name="zip" class="form-control" value="{{ old('zip', $location->zip ?? '') }}">
  </div>
  <div class="col-md-4">
    <label class="form-label">Country</label>
    <input name="country" class="form-control" value="{{ old('country', $location->country ?? '') }}">
  </div>
</div>
<div class="mb-3 mt-3">
  <label class="form-label">Phone</label>
  <input name="phone" class="form-control" value="{{ old('phone', $location->phone ?? '') }}">
</div>
<div class="form-check mb-2">
  <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
    @checked(old('is_active', $location->is_active ?? true))>
  <label class="form-check-label" for="is_active">Active</label>
</div>
<div class="form-check">
  <input class="form-check-input" type="checkbox" name="fulfills_online_orders" id="fulfills_online_orders" value="1"
    @checked(old('fulfills_online_orders', $location->fulfills_online_orders ?? true))>
  <label class="form-check-label" for="fulfills_online_orders">Fulfills online orders</label>
</div>
