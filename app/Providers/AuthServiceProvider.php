<?php

namespace App\Providers;

use App\Models\Bag;
use App\Models\Goods;
use App\Models\Order;
use App\Models\Reminder;
use App\Models\Route;
use App\Models\Store;
use App\Policies\BagPolicy;
use App\Policies\GoodsPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ReminderPolicy;
use App\Policies\RoutePolicy;
use App\Policies\StorePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Goods::class    => GoodsPolicy::class,
        Order::class    => OrderPolicy::class,
        Bag::class      => BagPolicy::class,
        Store::class    => StorePolicy::class,
        Reminder::class => ReminderPolicy::class,
        Route::class    => RoutePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        \Gate::define('profile-update', function ($user, $id) {
            return $user->id == $id;
        });
    }
}
