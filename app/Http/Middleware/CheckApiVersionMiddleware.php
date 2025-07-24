<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiVersionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return response()->json([
            'status' => false,
            'message' => 'Aplikasi Tidak Bisa Dipakai, Silahkan Update Ke Versi Terbaru',
        ], 426);

        // return $next($request);
    }
}
