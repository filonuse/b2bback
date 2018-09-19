<?php

namespace App\Providers;

use App\Models\Route;
use App\Models\Store;
use App\Models\User;
use App\Observers\RouteObserver;
use App\Observers\StoreObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Schema::defaultStringLength(191);

        // Observers
        User::observe(UserObserver::class);
        Route::observe(RouteObserver::class);
        Store::observe(StoreObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
