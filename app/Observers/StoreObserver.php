<?php

namespace App\Observers;


use App\Jobs\Logistics\StoreSyncRoutes;
use App\Models\Store;

class StoreObserver
{
    /**
     * Listen to the Store created event.
     *
     * @param  Store $store
     * @return void
     */
    public function created(Store $store)
    {
        StoreSyncRoutes::dispatch($store);
    }

    /**
     * Listen to the Store updated event.
     *
     * @param  Store $store
     * @return void
     */
    public function updated(Store $store)
    {
        StoreSyncRoutes::dispatch($store);
    }

    /**
     * Listen to the Store updated event.
     *
     * @param  Store $store
     * @return void
     */
    public function deleted(Store $store)
    {
        $store->routes()->detach();
    }
}