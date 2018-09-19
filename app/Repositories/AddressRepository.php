<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Address;

class AddressRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Address::class;
    }
}
