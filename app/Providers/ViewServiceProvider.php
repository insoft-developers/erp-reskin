<?php

namespace App\Providers;

use App\Models\MlAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Menggunakan View Composer untuk mengatur data global
        View::composer('*', function ($view) {
            // Cek apakah pengguna sudah login
            $id =  Session::get('user-id') ?? Session::get('id') ?? 0;
            $user = MlAccount::find($id);

            $conf = DB::table('ml_site_config')->first();
            $view->with('dataConfig', $conf);

            if ($user) {
                $view->with('dataUser', $user);

                if ($user->is_upgraded) {
                    $history = DB::table('subscription_logs as main')
                        ->select('plans.title')
                        ->join('subscription_plans as plans', 'plans.id', '=', 'main.subscription_id')
                        ->whereAccount_id($id)
                        ->orderBy('main.id', 'desc')
                        ->whereStatus(1)
                        ->first();

                    if (!$history) {
                        $owner = DB::table('owner_detail_users')->where('user_id', $id)->first();

                        if ($owner) {
                            $history = DB::table('subscription_logs as main')
                                ->select('plans.title')
                                ->join('subscription_plans as plans', 'plans.id', '=', 'main.subscription_id')
                                ->whereAccount_id($owner->owner_id)
                                ->orderBy('main.id', 'desc')
                                ->whereStatus(1)
                                ->first();

                                if (!$history && $owner->owner_id ===17600) {
                                    $history = (object) [
                                        'title' => 'PREMIUM USER'
                                    ];
                                }
                        }
                    }

                    $view->with('premiumTitle', $history ? $history->title : 'FREE USER');
                }
            }
        });
    }
}
