<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Bag;

class BagRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Bag::class;
    }

    /**
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filter($userId)
    {
        return $this->query()
            ->where('customer_id', $userId)
            ->with(['provider', 'goods.images']);
    }

    public function goodsCount($userId)
    {
        return $this->query()
            ->select('bag_goods.id')
            ->leftJoin('bag_goods as bg', 'bags.id', '=', 'bg.bag_id')
            ->where('bags.customer_id', $userId)
            ->count();
    }
}
