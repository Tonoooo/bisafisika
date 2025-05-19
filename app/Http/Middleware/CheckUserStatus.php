<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user sudah login
        if (auth()->check()) {
            // Jika status pending dan bukan di halaman waiting
            if (auth()->user()->status === 'pending' && !$request->is('teacher/waiting')) {
                return redirect()->route('teacher.waiting');
            }
            
            // Jika status verified dan di halaman waiting
            if (auth()->user()->status === 'verified' && $request->is('teacher/waiting')) {
                return redirect('/admin');
            }
        }

        return $next($request);
    }
}
