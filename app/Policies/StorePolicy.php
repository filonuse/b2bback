<?php

namespace App\Policies;

use App\Enums\RoleType;
use App\Models\User;
use App\Models\Store;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the store.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Store  $store
     * @return mixed
     */
    public function view(User $user, Store $store)
    {
        //
    }

    /**
     * Determine whether the user can create stores.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole(RoleType::CUSTOMER);
    }

    /**
     * Determine whether the user can update the store.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Store  $store
     * @return mixed
     */
    public function update(User $user, Store $store)
    {
        return $user->id === $store->user_id;
    }

    /**
     * Determine whether the user can delete the store.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Store  $store
     * @return mixed
     */
    public function delete(User $user, Store $store)
    {
        return $user->id === $store->user_id;
    }
}
