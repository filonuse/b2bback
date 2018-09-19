<?php

namespace App\Repositories\Criteria\Goods;


use Czim\Repository\Criteria\AbstractCriteria;
use Illuminate\Database\Eloquent\Builder;

class InRadiusDelivery extends AbstractCriteria
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
        return $model
            ->whereIn($this->column, function ($query) {
                return $query
                    ->select('r.user_id')
                    ->from('routes as r')
                    ->whereIn('r.id', function ($query) {
                        return $query
                            ->select('rs.route_id')
                            ->from('routes_stores as rs')
                            ->whereIn('rs.store_id', function ($query) {
                                return $query
                                    ->select('s.id')
                                    ->from('stores as s')
                                    ->where('s.user_id', $this->value);
                            });
                    });
            });
    }
}