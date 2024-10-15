<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // if ($this->app->environment('production')) {
        //     URL::forceScheme('https');
        // }
        // URL::forceScheme('http');

        Blade::directive('unslug', function (string $str) {
            return "<?php echo str_replace(['_'], ' ',  $str); ?>";
        });
    }
}
