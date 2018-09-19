<?php

namespace App\Repositories\Criteria\User;


use Czim\Repository\Criteria\AbstractCriteria;
use Illuminate\Database\Eloquent\Builder;

class Like extends AbstractCriteria
{
    /**
     * @var string|int
     */
    protected $value;

    /**
     * Like constructor.
     * @param $value
     */
    public function __construct(string $value)
    {
        $this->value  = $value;
    }

    /**
     * @param Builder $model
     * @return mixed
     */
    protected function applyToQuery($model)
    {
        return $model
            ->where('name', 'LIKE', "%{$this->value}%")
            ->orWhere('legal_name', 'LIKE', "%{$this->value}%");
    }
}