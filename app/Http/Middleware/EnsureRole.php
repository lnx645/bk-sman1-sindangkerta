<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memastikan pengguna sudah login dan memiliki salah satu peran yang
 * diizinkan untuk rute tersebut. Dipakai sebagai alias `role` di
 * bootstrap/app.php, misal: ->middleware(['auth', 'role:guru_bk']).
 *
 * Middleware ini adalah lapisan keamanan utama di sisi server. Menyembunyikan
 * menu di Blade saja TIDAK cukup — rute tetap harus melewati middleware ini.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda sedang dinonaktifkan. Hubungi admin.',
            ]);
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return $next($request);
    }
}
