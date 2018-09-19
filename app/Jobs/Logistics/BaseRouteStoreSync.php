<?php

namespace App\Jobs\Logistics;


use App\Models\Route;
use App\Models\Store;
use App\Services\GoogleService;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRouteStoreSync
{
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * @var GoogleService
     */
    public $googleService;

    /**
     * @var Model
     */
    public $model;

    /**
     * @var array
     */
    public $suitable = [];

    /**
     * BaseRouteStoreSync constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model         = $model;
        $this->googleService = new GoogleService();
    }

    /**
     * @param Route $route
     * @param Store $store
     * @return array
     */
    protected function getPointsInRadiusRoute(Route $route, Store $store, $related)
    {
        $directions = json_decode($route->directions);

        foreach ($directions as $direction) {
            $origin      = implode(',', $direction);
            $destination = $store->address->lat . ',' . $store->address->lng;
            $distance    = app_map_distance($origin, $destination);

            if ($distance <= $route->max_deviation) {
                $this->suitable[$$related->id][$distance]['origin']        = $origin;
                $this->suitable[$$related->id][$distance]['destination']   = $destination;
                $this->suitable[$$related->id][$distance]['max_deviation'] = $route->max_deviation;
            }
        }
    }

    /**
     * @param string $related
     */
    protected function createData(string $related)
    {
        $$related = $this->model->$related()->pluck('routes_stores.distance', $related . '.id')->toArray();

        foreach ($this->suitable as $id => $item) {
            if (key_exists($id, $$related)) {
                continue;
            }

            ksort($item);

            foreach ($item as $k => $distance) {
                $deviation = $this->googleService->getDrivingDistance($distance['origin'], $distance['destination']);

                if ($deviation <= $distance['max_deviation']) {
                    $$related[$id] = $deviation;
                    break;
                }
            }
        }
        // Prepare data for Sync
        array_walk($$related, function (&$item) {
            $item = ['distance' => $item];
        });
        // Execute the Sync
        $this->model->$related()->sync($$related);
        $this->model->update(['processed' => true]);
    }
}