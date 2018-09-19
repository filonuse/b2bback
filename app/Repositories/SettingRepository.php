<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Setting;

class SettingRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Setting::class;
    }
}
