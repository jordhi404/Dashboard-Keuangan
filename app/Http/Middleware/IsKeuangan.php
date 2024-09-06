<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsKeuangan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->kode_bagian !== 'k2' && Auth::user()->kode_bagian !== 'k45' && Auth::user()->kode_bagian !== 'k67' && Auth::user()->kode_bagian !== 'os26' && Auth::user()->kode_bagian !== 'k98') {
            abort(403, 'You are not authorized to access this page.');
        }
        return $next($request);
    }
}
