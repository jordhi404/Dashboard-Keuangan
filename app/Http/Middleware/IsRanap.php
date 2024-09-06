<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsRanap
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        switch (Auth::user()->kode_bagian) {
            case 'k13':
            case 'k14':
            case 'k15':
            case 'k16':
            case 'k41':
            case 'k45':
            case 'k58':
            case 'k59':
            case 'os29':
            case 'os26':
            case 'k98':
                return $next($request);
            default:
                abort(403, 'You are not authorized to access this page.');
        }
    }
}
