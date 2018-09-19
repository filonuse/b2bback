<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Status;

class StatusRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Status::class;
    }
}
