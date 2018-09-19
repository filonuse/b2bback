<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\News;

class NewsRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return News::class;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function latest()
    {
        return $this->query()
            ->with('images')
            ->latest('created_at');
    }
}
