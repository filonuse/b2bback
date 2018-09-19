<?php

namespace App\Repositories;

use App\Enums\RoleType;
use App\Repositories\Criteria\User\Like;
use App\Repositories\Criteria\User\OrderRelation;
use Czim\Repository\BaseRepository;

use App\Models\User;

class UserRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function allUsers(string $role = '')
    {
        return $this->query()
            ->whereHas('roles', function ($query) use ($role) {
                $query->when($role, function ($query) use ($role) {
                    return $query->where('name', '=', $role);
                }, function ($query) {
                    return $query->where('name', '!=', 'admin');
                });
            })
            ->orderBy('name');
    }

    /**
     * @param User $auth
     * @param null $whereLike
     * @return UserRepository
     */
    public function filter(User $auth, $whereLike = null)
    {
        if ($whereLike) {
            $this->pushCriteria(new Like($whereLike));
        }

        $this->pushCriteria(new OrderRelation($auth));

        return $this->applyCriteria();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function setRole(string $name)
    {
        $role = $this->app->make(RoleRepository::class);

        return $this->roles()->save($role->findWhere('name', $name));
    }


    /**
     * @param User $auth
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function contacts(User $auth)
    {
        $role          = $auth->roleName();
        $relatedColumn = ($role == RoleType::PROVIDER)
            ? 'customer_id'
            : 'provider_id';

        return $this->query()
            ->selectRaw('DISTINCT users.id, users.*')
            ->leftJoin('orders as o', "{$role}_id", \DB::raw($auth->id))
            ->leftJoin('user_messages as um1', 'um1.from_user_id', \DB::raw($auth->id))
            ->leftJoin('user_messages as um2', 'um2.to_user_id', \DB::raw($auth->id))
            ->orWhereRaw("users.id = o.{$relatedColumn}")
            ->orWhereRaw("users.id = um1.to_user_id")
            ->orWhereRaw("users.id = um2.from_user_id")
            ->orderBy('users.name', 'asc');
    }
}
