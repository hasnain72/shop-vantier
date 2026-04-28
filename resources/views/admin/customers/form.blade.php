@csrf

<div class="row g-4">
  <div class="col-lg-8">

    {{-- Name & Contact --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">Customer information</div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">First name <span class="text-danger">*</span></label>
            <input name="first_name" class="form-control @error('first_name') is-invalid @enderror"
              value="{{ old('first_name', $customer->first_name ?? '') }}" required>
            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Last name <span class="text-danger">*</span></label>
            <input name="last_name" class="form-control @error('last_name') is-invalid @enderror"
              value="{{ old('last_name', $customer->last_name ?? '') }}" required>
            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
              value="{{ old('email', $customer->email ?? '') }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input name="phone" class="form-control"
              value="{{ old('phone', $customer->phone ?? '') }}">
          </div>
        </div>
        @if(!isset($customer))
          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" autocomplete="new-password">
            </div>
            <div class="col-md-6 d-flex align-items-end pb-1">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="send_invite" id="send_invite" value="1">
                <label class="form-check-label" for="send_invite">Send account invite email</label>
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>

    {{-- Default Address --}}
    @if(!isset($customer))
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="fw-semibold">Default address</div>
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse"
            data-bs-target="#addressCollapse">Toggle</button>
        </div>
        <div class="collapse show" id="addressCollapse">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">First name</label>
              <input name="address[first_name]" class="form-control" value="{{ old('address.first_name') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Last name</label>
              <input name="address[last_name]" class="form-control" value="{{ old('address.last_name') }}">
            </div>
            <div class="col-12">
              <label class="form-label">Address</label>
              <input name="address[address1]" class="form-control" value="{{ old('address.address1') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">City</label>
              <input name="address[city]" class="form-control" value="{{ old('address.city') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">ZIP / Postal code</label>
              <input name="address[zip]" class="form-control" value="{{ old('address.zip') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Country code</label>
              <input name="address[country_code]" class="form-control" placeholder="US" maxlength="2"
                value="{{ old('address.country_code') }}">
            </div>
            <div class="col-12">
              <label class="form-label">Country</label>
              <input name="address[country]" class="form-control" value="{{ old('address.country') }}">
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

    {{-- Note --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <label class="form-label fw-semibold">Note</label>
        <textarea name="note" class="form-control" rows="3">{{ old('note', $customer->note ?? '') }}</textarea>
      </div>
    </div>

  </div>

  <div class="col-lg-4">

    {{-- State --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <label class="form-label fw-semibold">Account status</label>
        <select name="state" class="form-select @error('state') is-invalid @enderror">
          @foreach(['enabled' => 'Enabled', 'disabled' => 'Disabled', 'invited' => 'Invited', 'declined' => 'Declined'] as $val => $label)
            <option value="{{ $val }}" @selected(old('state', $customer->state ?? 'enabled') === $val)>{{ $label }}</option>
          @endforeach
        </select>
        @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>

    {{-- Marketing & Tax --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" value="1" id="accepts_marketing" name="accepts_marketing"
            @checked(old('accepts_marketing', $customer->accepts_marketing ?? false))>
          <label class="form-check-label" for="accepts_marketing">
            Customer agreed to receive marketing emails
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="1" id="tax_exempt" name="tax_exempt"
            @checked(old('tax_exempt', $customer->tax_exempt ?? false))>
          <label class="form-check-label" for="tax_exempt">Exempt from taxes</label>
        </div>
      </div>
    </div>

    {{-- Tags --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <label class="form-label fw-semibold">Tags</label>
        <select name="tags[]" id="customerTags" class="form-select" multiple>
          @foreach(old('tags', $customer->tags ?? []) as $tag)
            <option value="{{ $tag }}" selected>{{ $tag }}</option>
          @endforeach
        </select>
      </div>
    </div>

  </div>
</div>

@push('scripts')
<script>
  $('#customerTags').select2({ tags: true, tokenSeparators: [','], placeholder: 'Add tags…' });
</script>
@endpush
