<?php

namespace App\Jobs\Logistics;


use App\Models\Route;
use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RouteSyncStores extends BaseRouteStoreSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * RouteSyncStore constructor.
     * @param Route $route
     */
    public function __construct(Route $route)
    {
        parent::__construct($route);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $stores = Store::with('address')->get();

        // Get points in the radius route
        foreach ($stores as $store) {
            $this->getPointsInRadiusRoute($this->model, $store, 'store');
        }

        if (!empty($this->suitable)) {
            $this->createData('stores');
        }
    }
}
