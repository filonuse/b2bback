<?php

namespace App\Repositories\Criteria\User;


use App\Models\User;
use Czim\Repository\Criteria\AbstractCriteria;
use Illuminate\Database\Eloquent\Builder;

class OrderRelation extends AbstractCriteria
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $column = 'user_id';

    /**
     * @var string
     */
    protected $relatedColumn;

    /**
     * @var string
     */
    protected $keyColumn;

    /**
     * Relationship constructor.
     * @param User $related
     */
    public function __construct(User $related)
    {
        $role = $related->roleName();

        $this->user          = $related;
        $this->relatedColumn = $role . '_id';
        $this->keyColumn     = ($role == 'provider') ? 'customer_id' : 'provider_id';
    }

    /**
     * @param Builder $model
     * @return mixed
     */
    protected function applyToQuery($model)
    {
        return $model
            ->selectRaw('DISTINCT users.id, users.*')
            ->join('orders', $this->relatedColumn, \DB::raw("{$this->user->id}"))
            ->whereRaw("users.id = orders.{$this->keyColumn}");
    }
}