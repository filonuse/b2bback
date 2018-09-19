<?php

namespace App\Repositories\Criteria\Goods;


use Czim\Repository\Criteria\AbstractCriteria;
use Illuminate\Database\Eloquent\Builder;

class Owner extends AbstractCriteria
{
    /**
     * @var string
     */
    protected $column = 'user_id';

    /**
     * @var string|int
     */
    protected $value;

    /**
     * Owner constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param Builder $model
     * @return mixed
     */
    protected function applyToQuery($model)
    {
        return $model->where($this->column, '=', $this->value);
    }
}