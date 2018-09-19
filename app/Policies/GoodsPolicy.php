<?php

namespace App\Policies;

use App\Enums\RoleType;
use App\Models\User;
use App\Models\Goods;
use Illuminate\Auth\Access\HandlesAuthorization;

class GoodsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the goods.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Goods  $goods
     * @return mixed
     */
    public function view(User $user, Goods $goods)
    {
        //
    }

    /**
     * Determine whether the user can create goods.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole(RoleType::PROVIDER);
    }

    /**
     * Determine whether the user can update the goods.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Goods  $goods
     * @return mixed
     */
    public function update(User $user, Goods $goods)
    {
        return $user->id === $goods->user_id;
    }

    /**
     * Determine whether the user can delete the goods.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Goods  $goods
     * @return mixed
     */
    public function delete(User $user, Goods $goods)
    {
        return $user->id === $goods->user_id;
    }
}
