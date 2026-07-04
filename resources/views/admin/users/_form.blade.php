@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
    </ul>
  </div>
@endif

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control"
          value="{{ old('name', $user->name ?? '') }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control"
          value="{{ old('email', $user->email ?? '') }}" required>
      </div>

      <div class="col-md-6">
        <label class="form-label fw-semibold">
          Password @if(!$user)<span class="text-danger">*</span>@endif
        </label>
        <input type="password" name="password" class="form-control"
          {{ $user ? '' : 'required' }} minlength="8"
          placeholder="{{ $user ? 'Leave blank to keep current password' : 'Min. 8 characters' }}">
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">
          Confirm password @if(!$user)<span class="text-danger">*</span>@endif
        </label>
        <input type="password" name="password_confirmation" class="form-control"
          {{ $user ? '' : 'required' }} minlength="8">
      </div>

      <div class="col-md-6">
        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
        <select name="role" class="form-select" required>
          @foreach($roles as $r)
            <option value="{{ $r }}" @selected($currentRole === $r)>
              {{ ucwords(str_replace('_',' ',$r)) }}
            </option>
          @endforeach
        </select>
        <div class="form-text">
          <strong>Super Admin:</strong> full access (protected from demotion) · <strong>Admin:</strong> full admin panel access.
        </div>
      </div>

      <div class="col-md-6">
        <label class="form-label fw-semibold d-block">Status</label>
        <div class="form-check form-switch mt-2">
          <input class="form-check-input" type="checkbox" role="switch"
            id="is_active" name="is_active" value="1"
            @checked(old('is_active', $user->is_active ?? true))>
          <label class="form-check-label" for="is_active">Active</label>
        </div>
      </div>
    </div>
  </div>
</div>
