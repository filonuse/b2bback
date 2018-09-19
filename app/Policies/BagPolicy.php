<?php

namespace App\Policies;

use App\Enums\RoleType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BagPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create goods in the bag.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole(RoleType::CUSTOMER);
    }
}
