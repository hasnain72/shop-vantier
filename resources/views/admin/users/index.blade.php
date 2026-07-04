@extends('admin.layouts.app')
@section('title', 'Admin Users')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <div class="h4 mb-0">Admin Users</div>
    <div class="text-secondary small">{{ $users->total() }} admin/staff account(s)</div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-lg me-1"></i>New admin user
    </a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form method="GET" action="{{ route('admin.users.index') }}">
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
      <div class="row g-2 align-items-end">
        <div class="col-md-4">
          <input type="text" name="search" class="form-control form-control-sm"
            placeholder="Name or email…" value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
          <select name="role" class="form-select form-select-sm">
            <option value="">All roles</option>
            @foreach($roles as $r)
              <option value="{{ $r }}" @selected(request('role') === $r)>{{ ucwords(str_replace('_',' ',$r)) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
          <button class="btn btn-sm btn-primary w-50" type="submit"><i class="bi bi-search"></i></button>
          <a class="btn btn-sm btn-outline-secondary w-50" href="{{ route('admin.users.index') }}"><i class="bi bi-x-lg"></i></a>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Created</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $u)
          <tr>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>
              @foreach($u->roles as $role)
                <span class="badge {{ $role->name === 'super_admin' ? 'text-bg-danger' : ($role->name === 'admin' ? 'text-bg-primary' : 'text-bg-secondary') }}">
                  {{ ucwords(str_replace('_',' ',$role->name)) }}
                </span>
              @endforeach
            </td>
            <td>
              @if($u->is_active ?? true)
                <span class="badge text-bg-success">Active</span>
              @else
                <span class="badge text-bg-secondary">Inactive</span>
              @endif
            </td>
            <td class="text-secondary small">{{ optional($u->created_at)->format('Y-m-d') }}</td>
            <td class="text-end">
              <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
              @if(auth()->id() !== $u->id)
                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline"
                  onsubmit="return confirm('Delete this admin user?');">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-secondary py-4">No admin users found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">
  {{ $users->links() }}
</div>
@endsection
