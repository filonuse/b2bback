<?php

namespace App\Repositories\Criteria\Order;


use App\Models\User as UserModel;
use App\Enums\RoleType;
use Czim\Repository\Criteria\AbstractCriteria;
use Illuminate\Database\Eloquent\Builder;

class User extends AbstractCriteria
{
    /**
     * @var string
     */
    protected $column;

    /**
     * @var string
     */
    protected $localKeyForUser;

    /**
     * @var string|int
     */
    protected $value;

    /**
     * User constructor.
     * @param UserModel $user
     */
    public function __construct(UserModel $user)
    {
        if ($user->hasRole(RoleType::CUSTOMER)) {
            $this->column          = 'customer_id';
            $this->localKeyForUser = 'provider_id';
        } else {
            $this->column          = 'provider_id';
            $this->localKeyForUser = 'customer_id';
        }

        $this->value = $user->id;
    }

    /**
     * @param Builder $model
     * @return mixed
     */
    protected function applyToQuery($model)
    {
        return $model
            ->select('orders.*', 'u.name as user_name', 'u.legal_name as user_legal_name')
            ->leftJoin('users as u', 'u.id', '=', $this->localKeyForUser)
            ->where($this->column, $this->value);
    }
}