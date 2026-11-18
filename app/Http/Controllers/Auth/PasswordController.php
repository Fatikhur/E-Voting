<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function show(): View
    {
        return view('auth.password', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'current_password.current_password' => 'Kata sandi saat ini tidak cocok.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
            'password.min' => 'Kata sandi baru minimal harus 8 karakter.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        $this->auditLogService->log(
            action: 'change_password',
            eventType: 'security',
            description: "Pengguna {$user->username} berhasil memperbarui kata sandi.",
            user: $user
        );

        return back()->with('success', 'Kata sandi Anda berhasil diperbarui.');
    }
}
