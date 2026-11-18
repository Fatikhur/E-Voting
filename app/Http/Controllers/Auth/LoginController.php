<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\User;
use App\Services\AuditLogService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Username atau NIK wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $throttleKey = 'login:' . strtolower(trim($request->input('login'))) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            $this->auditLogService->log(
                action: 'login_throttled',
                eventType: 'security',
                description: "Percobaan login diblokir karena melebihi batas percobaan (Rate Limiting). IP: {$request->ip()}, Identifier: {$request->input('login')}",
                metadata: ['ip' => $request->ip(), 'seconds_remaining' => $seconds]
            );

            return back()
                ->withInput($request->only('login'))
                ->with('error', "Terlalu banyak percobaan masuk yang salah. Harap tunggu {$seconds} detik sebelum mencoba kembali.");
        }

        $input = trim($request->input('login'));
        $password = $request->input('password');

        // Dukung login via NIK (jika 16 digit angka) atau Username
        $user = null;
        if (ctype_digit($input) && strlen($input) === 16) {
            $nikHash = Resident::hashNik($input);
            $resident = Resident::where('nik_hash', $nikHash)->first();
            if ($resident) {
                $user = User::where('resident_id', $resident->id)->first();
            }
        }

        if (!$user) {
            $user = User::where('username', $input)->first();
        }

        if (!$user || !Auth::validate(['id' => $user->id, 'password' => $password])) {
            RateLimiter::hit($throttleKey, 60);

            $this->auditLogService->log(
                action: 'login_failed',
                eventType: 'auth',
                description: "Percobaan login gagal untuk identifier '{$input}'. Kata sandi salah atau pengguna tidak ditemukan.",
                metadata: ['identifier' => $input, 'ip' => $request->ip()]
            );

            return back()
                ->withInput($request->only('login'))
                ->with('error', 'Username/NIK atau kata sandi yang Anda masukkan tidak sesuai.');
        }

        // Cek status aktif akun
        if ($user->status !== 'active') {
            $this->auditLogService->log(
                action: 'login_rejected_inactive',
                eventType: 'auth',
                description: "Login ditolak untuk pengguna {$user->username} karena akun berstatus non-aktif.",
                metadata: ['user_id' => $user->id, 'status' => $user->status],
                user: $user
            );

            return back()
                ->withInput($request->only('login'))
                ->with('error', 'Akun Anda berstatus non-aktif. Silakan hubungi Administrator atau Pengurus RT/RW.');
        }

        // Login sukses
        RateLimiter::clear($throttleKey);
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $user->update([
            'last_login_at' => Carbon::now(),
            'last_login_ip' => $request->ip(),
        ]);

        $this->auditLogService->log(
            action: 'login_success',
            eventType: 'auth',
            description: "Pengguna {$user->username} ({$user->name}) berhasil masuk ke sistem dengan peran {$user->role_name}.",
            metadata: ['role' => $user->role_name, 'ip' => $request->ip()],
            user: $user
        );

        return redirect()->intended(route('dashboard'))
            ->with('success', "Selamat datang, {$user->name}!");
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            $this->auditLogService->log(
                action: 'logout',
                eventType: 'auth',
                description: "Pengguna {$user->username} telah keluar dari sistem.",
                metadata: ['ip' => $request->ip()],
                user: $user
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar.');
    }
}
