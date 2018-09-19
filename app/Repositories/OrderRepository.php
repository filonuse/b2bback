<?php

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Models\Bag;
use App\Models\User;
use App\Models\Order;
use App\Repositories\Criteria\Order\User as UserCriteria;
use App\Repositories\Criteria\Order\Status;
use App\Services\StatusService;
use Czim\Repository\BaseRepository;

class OrderRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Order::class;
    }

    /**
     * @param User $user
     * @param null $statusId
     * @return OrderRepository
     */
    public function filter(User $user, $statusId = null)
    {
        if ($statusId) {
            $this->pushCriteria(new Status($statusId));
        }

        $this->pushCriteria(new UserCriteria($user));

        return $this->applyCriteria();
    }

    /**
     * Checkout a order.
     *
     * @param Bag $bag
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function checkOut(Bag $bag, array $data)
    {
        \DB::beginTransaction();

        try {
            $order = $this->create([
                'customer_id'     => $bag->customer_id,
                'provider_id'     => $bag->provider_id,
                'store_id'        => $data['store_id'],
                'amount'          => $data['amount'],
                'amount_shipping' => $data['amount_shipping'],
            ]);

            $order->goods()->attach($data['goods']);

            // Set a status
            (new StatusService($order))->save(OrderStatus::PENDING);

            // Delete bag
            $bag->goods()->detach();
            $bag->delete();

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw new \Exception($e->getMessage());
        }

        return $order->refresh();
    }


}
