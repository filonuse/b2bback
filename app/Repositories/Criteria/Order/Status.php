<?php

namespace App\Repositories\Criteria\Order;


use Czim\Repository\Criteria\AbstractCriteria;
use Illuminate\Database\Eloquent\Builder;

class Status extends AbstractCriteria
{
    /**
     * @var string
     */
    protected $column = 'current_status_id';

    /**
     * @var string|int
     */
    protected $value;

    /**
     * Status constructor.
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
        return $model->where($this->column, $this->value);
    }
}