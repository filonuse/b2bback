<?php

namespace App\Repositories\Criteria\Goods;


use Czim\Repository\Criteria\AbstractCriteria;
use Illuminate\Database\Eloquent\Builder;

class Category extends AbstractCriteria
{
    /**
     * @var string
     */
    protected $column = 'category_id';

    /**
     * @var string|int
     */
    protected $value;

    /**
     * Category constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = explode(',', $value);
    }

    /**
     * @param Builder $model
     * @return mixed
     */
    protected function applyToQuery($model)
    {
        return $model->whereIn($this->column, $this->value);
    }
}