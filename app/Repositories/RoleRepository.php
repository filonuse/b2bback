<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Role;

class RoleRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Role::class;
    }
}
