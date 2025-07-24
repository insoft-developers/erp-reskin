<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Session;

class StorePreviousUrl
{
    public function handle($request, Closure $next)
    {
        if ($request->method() === 'GET' && !$request->ajax()) {
            Session::put('previous_url', url()->previous());
        }

        return $next($request);
    }
}
