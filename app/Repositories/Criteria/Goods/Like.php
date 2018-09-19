<?php

namespace App\Repositories\Criteria\Goods;


use Czim\Repository\Criteria\AbstractCriteria;
use Illuminate\Database\Eloquent\Builder;

class Like extends AbstractCriteria
{
    /**
     * @var string
     */
    protected $column = 'article';

    /**
     * @var string|int
     */
    protected $value;

    /**
     * Like constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value  = $value;
    }

    /**
     * @param Builder $model
     * @return mixed
     */
    protected function applyToQuery($model)
    {
        return $model->where($this->column, 'LIKE', "%{$this->value}%");
    }
}