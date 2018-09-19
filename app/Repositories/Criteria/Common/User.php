<?php

namespace App\Repositories\Criteria\Common;


use App\Models\User as Model;
use Czim\Repository\Criteria\AbstractCriteria;
use Illuminate\Database\Eloquent\Builder;

class User extends AbstractCriteria
{
    /**
     * @var string|int
     */
    protected $column;

    /**
     * @var string|int
     */
    protected $value;

    /**
     * User constructor.
     * @param Model $user
     * @param string $column
     */
    public function __construct(Model $user, $column = 'user_id')
    {
        $this->value  = $user->id;
        $this->column = $column;
    }

    /**
     * @param Builder $model
     * @return mixed
     */
    protected function applyToQuery($model)
    {
        return $model->where($this->column, $this->value);
    }
}