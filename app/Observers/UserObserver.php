<?php

namespace App\Observers;


use App\Enums\OrderStatus;
use App\Enums\RoleType;
use App\Models\User;
use App\Services\StatusService;

class UserObserver
{
    /**
     * Listen to the User updated event.
     *
     * @param  User $user
     * @return void
     * @throws \Exception
     */
    public function updated(User $user)
    {
        if ($user->banned) {
            $this->cancelOrders($user);
        }
    }

    /**
     * Listen to the User restored event.
     *
     * @param  User $user
     * @return void
     */
    public function restored(User $user)
    {
        if ($user->hasRole(RoleType::PROVIDER)) {
            $user->goods()->restore();
            $user->routes()->restore();
        } elseif ($user->hasRole(RoleType::CUSTOMER)) {
            $user->stores()->restore();
        }
    }

    /**
     * Listen to the User deleted event.
     *
     * @param  User $user
     * @return void
     * @throws \Exception
     */
    public function deleted(User $user)
    {
        if ($user->hasRole(RoleType::PROVIDER)) {
            $user->goods()->delete();
            $user->routes()->delete();
        } elseif ($user->hasRole(RoleType::CUSTOMER)) {
            $user->stores()->delete();
        }

        $this->cancelOrders($user);
    }

    /*
     | -------------------------------------------------------------------------
     |      Manipulation methods
     | -------------------------------------------------------------------------
     */

    /**
     * @param User $user
     * @throws \Exception
     */
    protected function cancelOrders(User $user)
    {
        $orders = $user->orders($user->roleName())
            ->whereHas('status', function ($query) {
                return $query
                    ->where('name', '=', OrderStatus::PENDING)
                    ->orWhere('name', '=', OrderStatus::PROCESSED)
                    ->orWhere('name', '=', OrderStatus::SHIPPED);
            })->get();

        foreach ($orders as $order) {
            (new StatusService($order))->save(OrderStatus::CANCELED);
        }
    }
}