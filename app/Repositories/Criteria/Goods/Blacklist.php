<?php

namespace App\Repositories\Criteria\Goods;


use Czim\Repository\Criteria\AbstractCriteria;
use Illuminate\Database\Eloquent\Builder;

class Blacklist extends AbstractCriteria
{
    /**
     * @var int
     */
    protected $authId;

    /**
     * @var string
     */
    protected $column = 'goods.user_id';

    /**
     * @var array
     */
    protected $value;

    /**
     * Blacklist constructor.
     * @param int $authId
     */
    public function __construct(int $authId)
    {
        $this->authId = $authId;
    }

    /**
     * @param Builder $model
     * @return mixed
     */
    protected function applyToQuery($model)
    {
        return $model
            ->whereNotIn($this->column , function ($query) {
                return $query
                    ->select('u.id')
                    ->from('users as u')
                    ->where('u.banned', true)
                    ->orWhereNotNull('u.deleted_at');
            })
            ->whereNotIn($this->column , function ($query) {
                return $query
                    ->select('b1.blocked_user_id')
                    ->from('blacklist as b1')
                    ->whereRaw("b1.user_id = {$this->authId}");
            })
            ->whereNotIn($this->column , function ($query) {
                return $query
                    ->select('b2.user_id')
                    ->from('blacklist as b2')
                    ->whereRaw("b2.blocked_user_id = {$this->authId}");
            });
    }
}