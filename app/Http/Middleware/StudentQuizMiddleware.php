<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentQuizMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Jika user adalah super_admin, izinkan akses ke semua quiz
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // Jika user adalah siswa, batasi akses ke quiz yang tersedia untuk sekolah mereka
        if ($user->hasRole('siswa')) {
            $schoolId = $user->school_id;
            $level = $user->level;
            $class = $user->class;
            
            // Modifikasi query untuk hanya menampilkan quiz yang sesuai dengan:
            // 1. Sekolah yang sama
            // 2. Level yang sama
            // 3. Kelas yang sama
            $request->merge([
                'school_id' => $schoolId,
                'level' => $level,
                'class' => $class
            ]);
        }

        return $next($request);
    }
}
