<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SchoolLeaderboardMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Jika user adalah super_admin, izinkan akses ke semua leaderboard
        if ($user->hasAnyRole(['super_admin'])) {
            return $next($request);
        }

        // Jika user adalah siswa, batasi akses ke leaderboard sekolah mereka
        if ($user->hasAnyRole(['siswa'])) {
            $schoolId = $user->school_id;
            
            // Modifikasi query untuk hanya menampilkan data dari sekolah yang sama
            $request->merge(['school_id' => $schoolId]);
        }

        return $next($request);
    }
}
