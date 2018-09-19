<?php

namespace App\Policies;

use App\Enums\RoleType;
use App\Models\User;
use App\Models\Route;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoutePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the route.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Route  $route
     * @return mixed
     */
    public function view(User $user, Route $route)
    {
        return $user->id === $route->user_id;
    }

    /**
     * Determine whether the user can create routes.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole(RoleType::PROVIDER);
    }

    /**
     * Determine whether the user can update the route.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Route  $route
     * @return mixed
     */
    public function update(User $user, Route $route)
    {
        return $user->id === $route->user_id;
    }

    /**
     * Determine whether the user can delete the route.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Route  $route
     * @return mixed
     */
    public function delete(User $user, Route $route)
    {
        return $user->id === $route->user_id;
    }
}
