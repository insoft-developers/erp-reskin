<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HandleSession
{
    public function handle(Request $request, Closure $next)
    {
        $userKey = $request->user_key ?? '';
        if ($userKey) {
            $account = DB::table('ml_accounts')->whereUser_key($userKey)->first();
            if ($account) {
                $request->session()->invalidate();
                $request->session()->flush();
                $request->session()->regenerate();
                session(['id' => $account->id]);
            } else {
                return abort(404);
            }
        }

        return $next($request);
    }
}
