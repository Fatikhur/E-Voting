<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Arahkan pengguna ke antarmuka dashboard sesuai rolenya.
     */
    public function index(): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        return match ($user->role_name) {
            'superadmin' => redirect()->route('superadmin.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            'pengawas' => redirect()->route('pengawas.dashboard'),
            default => redirect()->route('voter.dashboard'),
        };
    }
}
