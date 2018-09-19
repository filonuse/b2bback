<?php

namespace App\Repositories;

use App\Models\User;
use Czim\Repository\BaseRepository;
use App\Models\Route;

class RouteRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Route::class;
    }

    /**
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filter(User $user)
    {
        return $this->query()->where('user_id', $user->id);
    }

    /**
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function createWithRelations(array $data)
    {
        \DB::beginTransaction();

        try {
            $route = $this->create([
                'user_id'       => $data['user_id'],
                'time_start'    => $data['time_start'],
                'time_finish'   => $data['time_finish'],
                'max_deviation' => $data['max_deviation'],
                'processed'     => false,
                'directions'    => $data['directions'],
            ]);
            // Attach addresses
            $route->addresses()->attach($this->getAddressesIds($data['addresses']));
            // Create deviations from route
            $this->syncDeviations($route, $data['deviations']);

            \DB::commit();

            return $route;
        } catch (\Exception $e) {
            \DB::rollBack();
            abort(500, $e->getMessage());
        }
    }

    /**
     * @param Route $route
     * @param array $data
     * @return Route
     */
    public function updateWithRelations(Route $route, array $data)
    {
        \DB::beginTransaction();

        try {
            $routeData = [
                'time_start'    => $data['time_start'],
                'time_finish'   => $data['time_finish'],
                'max_deviation' => $data['max_deviation'],
                'processed'     => false,
            ];

            if (key_exists('directions', $data)) {
                $routeData['directions'] = $data['directions'];
            }
            // Update the route
            $route->update($routeData);
            // Attach addresses
            $route->addresses()->sync($this->getAddressesIds($data['addresses']));
            // Update deviations from route
            $this->syncDeviations($route, $data['deviations']);

            \DB::commit();

            return $route;
        } catch (\Exception $e) {
            \DB::rollBack();
            abort(500, $e->getMessage());
        }
    }

    /**
     * @param array $data
     * @return array
     */
    private function getAddressesIds(array $data)
    {
        $ids        = [];
        $repository = app('App\Repositories\AddressRepository');

        foreach ($data as $point) {
            $address = $repository->query()->firstOrCreate($point);
            array_push($ids, $address->id);
        }

        return $ids;
    }

    /**
     * @param Route $route
     * @param array $data
     */
    private function syncDeviations(Route $route, array $data)
    {
        $repository = app('App\Repositories\DeviationRepository');
        // Delete existing
        $repository->query()->where('route_id', $route->id)->delete();
        // Create
        foreach ($data as $deviation) {
            $deviation['route_id'] = $route->id;
            $repository->create($deviation);
        }
    }
}
