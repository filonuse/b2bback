<?php

namespace App\Observers;


use App\Jobs\Logistics\RouteSyncStores;
use App\Models\Route;

class RouteObserver
{
    /**
     * Listen to the Route created event.
     *
     * @param  Route  $route
     * @return void
     */
    public function created(Route $route)
    {
        RouteSyncStores::dispatch($route);
    }

    /**
     * Listen to the Route updated event.
     *
     * @param  Route  $route
     * @return void
     */
    public function updated(Route $route)
    {
        RouteSyncStores::dispatch($route);
    }

    /**
     * Listen to the Route deleted event.
     *
     * @param  Route  $route
     * @return void
     */
    public function deleted(Route $route)
    {
        $route->stores()->detach();
    }
}