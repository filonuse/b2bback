<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Reminder;

class ReminderRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Reminder::class;
    }
}
