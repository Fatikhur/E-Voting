<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Rw;
use App\Models\User;
use App\Models\Village;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function index(Request $request): View
    {
        $query = User::with(['role', 'rw', 'rt']);

        if ($request->filled('role')) {
            $query->where('role_name', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('superadmin.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::all();
        $villages = Village::all();
        $rws = Rw::all();

        return view('superadmin.users.create', compact('roles', 'villages', 'rws'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role_name' => 'required|in:superadmin,admin,pengawas,voter',
            'phone' => 'nullable|string|max:25',
            'village_id' => 'nullable|exists:villages,id',
            'rw_id' => 'nullable|exists:rws,id',
        ]);

        $role = Role::where('name', $request->role_name)->first();

        $user = User::create([
            'name' => $request->name,
            'username' => trim($request->username),
            'email' => $request->email ? trim($request->email) : null,
            'password' => Hash::make($request->password),
            'role_name' => $request->role_name,
            'role_id' => $role?->id,
            'village_id' => $request->village_id,
            'rw_id' => $request->rw_id,
            'phone' => $request->phone,
            'status' => 'active',
        ]);

        if ($role) {
            $user->roles()->sync([$role->id]);
        }

        $this->auditLogService->log(
            action: 'create_user',
            eventType: 'security',
            description: "Superadmin membuat akun baru: {$user->username} ({$user->name}) dengan peran {$user->role_name}.",
            metadata: ['user_id' => $user->id, 'role' => $user->role_name]
        );

        return redirect()->route('superadmin.users.index')
            ->with('success', "Akun pengguna '{$user->username}' berhasil dibuat.");
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        $this->auditLogService->log(
            action: 'toggle_user_status',
            eventType: 'security',
            description: "Superadmin mengubah status pengguna {$user->username} menjadi {$newStatus}.",
            metadata: ['user_id' => $user->id, 'new_status' => $newStatus]
        );

        return back()->with('success', "Status akun '{$user->username}' berhasil diubah menjadi {$newStatus}.");
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'new_password' => 'required|string|min:8',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
            'must_change_password' => true,
        ]);

        $this->auditLogService->log(
            action: 'reset_user_password',
            eventType: 'security',
            description: "Superadmin mereset kata sandi untuk pengguna {$user->username}.",
            metadata: ['user_id' => $user->id]
        );

        return back()->with('success', "Kata sandi untuk '{$user->username}' berhasil direset.");
    }
}
