<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Blade::directive('role', function ($expression) {
            $expression = trim($expression, "()");
            $roles = explode(',', $expression);
            $conditions = [];

            foreach ($roles as $role) {
                $role = trim($role, '\'" ');
                if (strpos($role, '!') === 0) {
                    $role = substr($role, 1);
                    $conditions[] = "session('role') != '$role'";
                } else {
                    $conditions[] = "session('role') == '$role'";
                }
            }

            $conditionString = implode(' || ', $conditions);
            return "<?php if($conditionString): ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('can', function ($expression) {
            return "<?php if(collect(session('permissions'))->contains($expression)): ?>";
        });

        Blade::directive('endcan', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('currency', function ($input) {
            return "Rp. <?php echo number_format($input,0,',','.'); ?>";
        });
        View::composer('*', function ($view) {
            $cart = session()->get('cart', []);
            $totalQuantity = 0;
            foreach ($cart as $product) {
                $totalQuantity += $product['quantity'];
            }
            $view->with('totalQuantity', $totalQuantity);
        });
    }
}
