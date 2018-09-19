<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Image;

class ImageRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Image::class;
    }
}
