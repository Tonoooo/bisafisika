<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    // public function handle(Request $request, Closure $next)
    // {
    //     if (auth()->user() && auth()->user()->hasRole('super_admin')) {
    //         return $next($request);
    //     }

    //     return redirect('/')->with('error', 'Unauthorized access');
}
