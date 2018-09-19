<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reminder;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReminderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the reminder.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Reminder  $reminder
     * @return mixed
     */
    public function view(User $user, Reminder $reminder)
    {
        return $user->id === $reminder->user_id;
    }

    /**
     * Determine whether the user can create reminders.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the reminder.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Reminder  $reminder
     * @return mixed
     */
    public function update(User $user, Reminder $reminder)
    {
        return $user->id === $reminder->user_id;
    }

    /**
     * Determine whether the user can delete the reminder.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Reminder  $reminder
     * @return mixed
     */
    public function delete(User $user, Reminder $reminder)
    {
        return $user->id === $reminder->user_id;
    }
}
