<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentProfileMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Jika user adalah super_admin, izinkan akses ke semua profil
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // Jika user adalah siswa, batasi akses hanya ke profil mereka sendiri
        if ($user->hasRole('siswa')) {
            $requestedUserId = $request->route('user');
            
            if ($requestedUserId && $requestedUserId != $user->id) {
                abort(403, 'Anda tidak memiliki akses untuk mengubah profil pengguna lain.');
            }
        }

        return $next($request);
    }
}
