<?php

namespace App\Services;


use App\Models\Store;

class RouteService
{
    /**
     * Calculate the cost of delivery to specified store
     *
     * @param Store $store
     * @param int $providerId
     * @param double $amountOrder
     * @return float|int
     *
     * @throws \Exception
     */
    public static function calcAmountShipping(Store $store, $providerId, $amountOrder = 0.00)
    {
        $amounts = [];
        $routes  = $store->availableRoutes($providerId)->with('deviations')->get();

        if ($routes->isEmpty()) {
            abort(405, 'The store is not within the radius of delivery');
        }

        // Calculate the cost of delivery to specified route
        foreach ($routes as $route) {
            if ($route->deviations->isEmpty())
                continue;

            $deviations = $route->deviations->sortByDesc('distance');

            foreach ($deviations as $deviation) {
                if ($route->pivot->distance <= $deviation->distance) {
                    $amounts[] = $deviation->price > 0
                        ? $deviation->price
                        : ($amountOrder / (100 * $deviation->percent));
                }
            }
        }

        return !empty($amounts) ? min($amounts) : 0;
    }
}