<?php

namespace App\Repositories\Criteria\Status;


use Czim\Repository\Criteria\AbstractCriteria;
use Illuminate\Database\Eloquent\Builder;

class Order extends AbstractCriteria
{
    /**
     * @var string
     */
    protected $column = 'type';

    /**
     * @var string|int
     */
    protected $value = 'order';

    /**
     * @param Builder $model
     * @return mixed
     */
    protected function applyToQuery($model)
    {
        return $model->where($this->column, $this->value);
    }
}