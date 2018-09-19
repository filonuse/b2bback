<?php

namespace App\Jobs\Logistics;


use App\Models\Route;
use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StoreSyncRoutes extends BaseRouteStoreSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * StoreSyncRoutes constructor.
     * @param Store $store
     */
    public function __construct(Store $store)
    {
        parent::__construct($store);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $routes = Route::query()->where('activated', true)->get();

        // Get points in the radius route
        foreach ($routes as $route) {
            $this->getPointsInRadiusRoute($route, $this->model, 'route');
        }

        if (!empty($this->suitable)) {
            $this->createData('routes');
        }
    }
}
