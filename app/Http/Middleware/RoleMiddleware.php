<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        if (empty($roles)) {
            return $next($request);
        }

        // Superadmin memiliki akses ke semua area administratif
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (!$user->hasRole($roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Anda tidak memiliki wewenang untuk tindakan ini.',
                ], 403);
            }

            abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
