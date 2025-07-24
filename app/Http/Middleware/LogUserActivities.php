<?php

namespace App\Http\Middleware;

use App\Models\UserActivityLogs;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivities
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session('id')) {
            UserActivityLogs::create([
                'user_id' => session('id'),
                'page' => $request->path(),
            ]);
        }

        return $next($request);
    }
}
