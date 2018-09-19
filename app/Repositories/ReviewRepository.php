<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Review;

class ReviewRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Review::class;
    }

    /**
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filter(array $data)
    {
        return $this->query()
            ->where('reviewable_id', $data['reviewable_id'])
            ->where('reviewable_type', $data['reviewable_type']);
    }
}
