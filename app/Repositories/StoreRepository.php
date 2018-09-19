<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Store;

class StoreRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Store::class;
    }

    /**
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function createWithAddress(array $data)
    {
        $address = app('App\Repositories\AddressRepository')->query()
            ->firstOrCreate($data['address']);

        return $this->create([
            'user_id'    => $data['user_id'],
            'name'       => $data['name'],
            'legal_data' => $data['legal_data'],
            'address_id' => $address->id,
        ]);
    }

    /**
     * @param array $data
     * @param Store $store
     * @return bool
     */
    public function updateWithAddress(array $data, Store $store)
    {
        $address = app('App\Repositories\AddressRepository')->query()
            ->firstOrCreate($data['address']);

        return $store->update([
            'name'       => $data['name'],
            'legal_data' => $data['legal_data'],
            'address_id' => $address->id,
        ]);
    }
}
