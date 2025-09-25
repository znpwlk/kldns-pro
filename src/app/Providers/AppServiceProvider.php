<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('safeconfig', function ($expression) {
            return "<?php \$__val = config($expression); echo e(is_array(\$__val) ? implode(',', \$__val) : (is_scalar(\$__val) ? \$__val : '')); ?>";
        });
    }
}
