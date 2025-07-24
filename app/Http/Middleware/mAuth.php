<?php

namespace App\Http\Middleware;

use App\Models\Account;
use App\Traits\OmzetTrait;
use App\Traits\PremiumBlockingTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Session;
use Illuminate\Support\Facades\Route;

class mAuth
{
    use PremiumBlockingTrait, OmzetTrait;

    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->session()->has('id') && $request->session()->has('username') && $request->session()->has('token')) {
            // Check current route path
            $currentPath = $request->path();

           

            $startOfMonthsAgo = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s');
            $endOfMonthsAgo = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d H:i:s');


            $user = Account::find($request->session()->get('id'));
            
            $summary = $this->get_summary($startOfMonthsAgo, $endOfMonthsAgo, $request->session()->get('id'));
            $profit = $this->count_net_profit($startOfMonthsAgo, $endOfMonthsAgo, $request->session()->get('id'));
            $nett_profit = $profit['bersih'];
            $gros_profit = $profit['kotor'];
            $validation = $this->get_premium_validation();

           
            if ($nett_profit > $validation->max_net_profit || $gros_profit > $validation->max_gross_profit || $summary > $validation->max_summary) {

                // If already on /premium, do not redirect
                if ($currentPath === 'premium') {
                    return $next($request);
                }

                // Check if current route is premium.store
                if (str_contains($currentPath, 'premium/')) {
                    return $next($request);
                }

                if (!$user->is_upgraded) {
                    return redirect('premium')->with('error', 'premium blocking');
                } else {
                    return $next($request);
                }
            }

            
            // Continue to next middleware or controller
            return $next($request);
        } else {
            return redirect('frontend_login');
        }
    }
}
