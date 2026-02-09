<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- Mana shu qator muhim!

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
        // Agar sayt Railwayda (production) bo'lsa, majburan HTTPS qilsin
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
