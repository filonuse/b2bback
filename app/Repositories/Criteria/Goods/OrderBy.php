<?php

namespace App\Repositories\Criteria\Goods;


use Czim\Repository\Criteria\AbstractCriteria;
use Illuminate\Database\Eloquent\Builder;

class OrderBy extends AbstractCriteria
{
    /**
     * @var string
     */
    protected $column = 'article';

    /**
     * @var string|int
     */
    protected $direction;

    /**
     * OrderBy constructor.
     * @param $data
     */
    public function __construct($data)
    {
        list($this->column, $this->direction) = explode(',', $data);
    }

    /**
     * @param Builder $model
     * @return mixed
     */
    protected function applyToQuery($model)
    {
        return $model->orderBy($this->column, $this->direction);
    }
}