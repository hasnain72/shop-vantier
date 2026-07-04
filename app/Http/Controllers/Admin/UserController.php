<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /** Roles that grant admin-panel access. Any user with at least one is treated as an "admin". */
    private const ADMIN_ROLES = ['super_admin', 'admin', 'staff'];

    public function index(Request $request): View
    {
        $query = User::with('roles')
            ->whereHas('roles', fn ($q) => $q->whereIn('name', self::ADMIN_ROLES));

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            if (in_array($role, self::ADMIN_ROLES, true)) {
                $query->whereHas('roles', fn ($q) => $q->where('name', $role));
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => self::ADMIN_ROLES,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => self::ADMIN_ROLES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => ['required', Rule::in(self::ADMIN_ROLES)],
            'is_active'=> 'nullable|boolean',
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        $role = Role::firstOrCreate(['name' => $data['role'], 'guard_name' => 'web']);
        $user->syncRoles([$role->name]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Admin user created.');
    }

    public function edit(User $user): View
    {
        $user->load('roles');

        return view('admin.users.edit', [
            'user'  => $user,
            'roles' => self::ADMIN_ROLES,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => ['required', Rule::in(self::ADMIN_ROLES)],
            'is_active'=> 'nullable|boolean',
        ]);

        // Guard: don't allow the last super_admin to demote/deactivate themselves out of admin access.
        $isSuperAdmin = $user->hasRole('super_admin');
        $lastSuperAdmin = $isSuperAdmin
            && User::role('super_admin')->count() <= 1;

        if ($lastSuperAdmin && $data['role'] !== 'super_admin') {
            return back()->withInput()->with('error', 'Cannot demote the last super admin.');
        }
        if ($lastSuperAdmin && !($data['is_active'] ?? true)) {
            return back()->withInput()->with('error', 'Cannot deactivate the last super admin.');
        }

        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->is_active = (bool) ($data['is_active'] ?? false);
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        $user->syncRoles([$data['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Admin user updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->hasRole('super_admin') && User::role('super_admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the last super admin.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Admin user deleted.');
    }
}
