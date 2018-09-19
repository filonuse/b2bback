<?php

namespace App\Repositories;

use App\Models\RouteDeviation;
use Czim\Repository\BaseRepository;

class DeviationRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return RouteDeviation::class;
    }
}
