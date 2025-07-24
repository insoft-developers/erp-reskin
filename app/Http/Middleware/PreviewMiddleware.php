<?php

namespace App\Http\Middleware;

use App\Models\MlAccount;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreviewMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->query('user_key');

        // Memeriksa apakah token ada di tabel ml_account
        $account = MlAccount::where('token', $token)->first();
        if (!$account) {
            return response()->json(['error' => 'Unauthorized'], 401);
        } else {
            session(['id' => $account->id]);
        }

        return $next($request);
    }
}
