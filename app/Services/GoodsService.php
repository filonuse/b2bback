<?php

namespace App\Services;


use App\Enums\GoodsAction;
use App\Models\Goods;

class GoodsService
{
    /**
     * @param Goods $goods
     * @param $action
     * @param $quantity
     * @return null
     */
    public static function syncQuantity(Goods $goods, $action, $quantity)
    {
        switch ($action) {
            case GoodsAction::RESERVE:
                $goods->decrement('quantity_available', $quantity);
                $goods->increment('quantity_reserve', $quantity);
                return TRUE;

            case GoodsAction::WRITE_OFF:
                $goods->decrement('quantity_actual', $quantity);
                $goods->decrement('quantity_reserve', $quantity);
                return TRUE;

            case GoodsAction::REVERT:
                $goods->increment('quantity_available', $quantity);
                $goods->decrement('quantity_reserve', $quantity);
                return TRUE;

            default:
                return FALSE;
        }
    }

    /**
     * @param integer $goodsId
     * @param $quantity
     * @return bool
     */
    public static function isAvailableQty(int $goodsId, $quantity)
    {
        $repository = app('App\Repositories\GoodsRepository');

        return $repository->query()
            ->where('id', $goodsId)
            ->where('quantity_available', '>=', $quantity)
            ->exists();
    }
}