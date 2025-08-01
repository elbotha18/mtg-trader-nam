<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        // Set the default string length for database columns to avoid issues with older MySQL versions
        Schema::defaultStringLength(191);
        
        // Set the default locale or timezone here
        config(['app.locale' => 'en']);
        config(['app.timezone' => 'Africa/Windhoek']);
    }
}
