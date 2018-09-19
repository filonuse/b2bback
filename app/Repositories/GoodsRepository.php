<?php

namespace App\Repositories;


use App\Models\Goods;
use App\Repositories\Criteria\Goods\Blacklist;
use Czim\Repository\BaseRepository;

class GoodsRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Goods::class;
    }

    /**
     * @param int $authId
     * @param array $filters
     * @return GoodsRepository
     */
    public function filter($authId, array $filters)
    {
        foreach ($filters as $key => $filter) {
            $criteria = '\\App\\Repositories\\Criteria\\Goods\\' . studly_case($key);
            if (class_exists($criteria)) {
                $this->pushCriteria(new $criteria($filter));
            }
        }

        // Default
        $this->pushCriteria(new Blacklist($authId));

        return $this->applyCriteria();
    }
}
