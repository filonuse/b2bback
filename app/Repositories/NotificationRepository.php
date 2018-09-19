<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Notification;

class NotificationRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Notification::class;
    }
}
