<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Category;

class CategoryRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Category::class;
    }
}
