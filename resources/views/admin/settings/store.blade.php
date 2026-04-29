@extends('admin.layouts.app')
@section('title', 'Store Settings')

@section('content')
<div class="h4 mb-3">Store settings</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<form method="POST" action="{{ route('admin.settings.store.update') }}" enctype="multipart/form-data">
  @csrf @method('PUT')

  {{-- Tabs --}}
  <ul class="nav nav-tabs mb-3" id="settingsTabs">
    <li class="nav-item"><a class="nav-link active" href="#general" data-bs-toggle="tab">General</a></li>
    <li class="nav-item"><a class="nav-link" href="#formats" data-bs-toggle="tab">Standards & formats</a></li>
    <li class="nav-item"><a class="nav-link" href="#media" data-bs-toggle="tab">Media</a></li>
  </ul>

  <div class="tab-content">
    {{-- General --}}
    <div class="tab-pane fade show active" id="general">
      <div class="card border-0 shadow-sm mb-3" style="max-width:640px;">
        <div class="card-body">
          <div class="fw-semibold mb-3">Store details</div>
          <div class="mb-3">
            <label class="form-label">Store name <span class="text-danger">*</span></label>
            <input name="store_name" class="form-control @error('store_name') is-invalid @enderror"
              value="{{ old('store_name', $settings['store_name'] ?? '') }}" required>
            @error('store_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Store email <span class="text-danger">*</span></label>
            <input name="store_email" type="email" class="form-control @error('store_email') is-invalid @enderror"
              value="{{ old('store_email', $settings['store_email'] ?? '') }}" required>
            @error('store_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input name="store_phone" class="form-control"
              value="{{ old('store_phone', $settings['store_phone'] ?? '') }}">
          </div>
          <div>
            <label class="form-label">Address</label>
            <textarea name="store_address" class="form-control" rows="3"
              >{{ old('store_address', $settings['store_address'] ?? '') }}</textarea>
          </div>
        </div>
      </div>
    </div>

    {{-- Standards & formats --}}
    <div class="tab-pane fade" id="formats">
      <div class="card border-0 shadow-sm mb-3" style="max-width:640px;">
        <div class="card-body">
          <div class="fw-semibold mb-3">Standards & formats</div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Currency <span class="text-danger">*</span></label>
              <select name="currency" class="form-select">
                @foreach(['USD','EUR','GBP','PKR','AED','CAD','AUD'] as $c)
                  <option value="{{ $c }}" {{ ($settings['currency'] ?? 'USD') === $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Timezone</label>
              <select name="timezone" class="form-select">
                @foreach(['Asia/Karachi','UTC','America/New_York','Europe/London','Asia/Dubai','Asia/Singapore'] as $tz)
                  <option value="{{ $tz }}" {{ ($settings['timezone'] ?? 'Asia/Karachi') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Weight unit</label>
              <select name="weight_unit" class="form-select">
                @foreach(['kg','g','lb','oz'] as $u)
                  <option value="{{ $u }}" {{ ($settings['weight_unit'] ?? 'kg') === $u ? 'selected' : '' }}>{{ strtoupper($u) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Order ID prefix</label>
              <input name="order_prefix" class="form-control"
                value="{{ old('order_prefix', $settings['order_prefix'] ?? '') }}" placeholder="e.g. WAS">
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Media --}}
    <div class="tab-pane fade" id="media">
      <div class="card border-0 shadow-sm mb-3" style="max-width:640px;">
        <div class="card-body">
          <div class="fw-semibold mb-3">Store media</div>
          @php $logo = \App\Models\StoreSetting::where('key','store_logo')->value('value'); @endphp
          <div class="mb-3">
            <label class="form-label">Logo</label>
            @if($logo)
              <div class="mb-2"><img src="{{ asset('storage/'.$logo) }}" alt="Logo" style="max-height:64px;"></div>
            @endif
            <input type="file" name="logo" class="form-control" accept="image/*">
          </div>
          @php $favicon = \App\Models\StoreSetting::where('key','store_favicon')->value('value'); @endphp
          <div>
            <label class="form-label">Favicon</label>
            @if($favicon)
              <div class="mb-2"><img src="{{ asset('storage/'.$favicon) }}" alt="Favicon" style="max-height:32px;"></div>
            @endif
            <input type="file" name="favicon" class="form-control" accept="image/*">
            <div class="form-text">32×32 px recommended.</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary mt-2">Save settings</button>
</form>
@endsection
