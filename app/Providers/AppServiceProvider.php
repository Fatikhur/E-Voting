<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Rate Limiting untuk Login Brute-force Protection
        RateLimiter::for('login', function (Request $request) {
            $identifier = (string) ($request->input('username') ?: $request->ip());
            return Limit::perMinute(5)->by($identifier)->response(function () {
                return back()->with('error', 'Terlalu banyak percobaan masuk yang gagal. Silakan tunggu 1 menit sebelum mencoba kembali.');
            });
        });

        // 2. Rate Limiting untuk Voting Endpoint (Double Request / Spam Protection)
        RateLimiter::for('voting', function (Request $request) {
            $userKey = $request->user() ? 'user:' . $request->user()->id : 'ip:' . $request->ip();
            return Limit::perMinute(10)->by($userKey)->response(function () {
                return back()->with('error', 'Terlalu banyak permintaan voting dalam waktu singkat. Harap tunggu sesaat.');
            });
        });

        // 3. Authorization Gates
        Gate::define('manage-system', function (User $user) {
            return $user->isSuperAdmin();
        });

        Gate::define('manage-election', function (User $user) {
            return $user->isSuperAdmin() || $user->isAdmin();
        });

        Gate::define('manage-residents', function (User $user) {
            return $user->isSuperAdmin() || $user->isAdmin();
        });

        Gate::define('supervise-election', function (User $user) {
            return $user->isSuperAdmin() || $user->isPengawas();
        });

        Gate::define('cast-vote', function (User $user) {
            return $user->isVoter();
        });
    }
}
