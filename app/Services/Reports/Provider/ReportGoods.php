<?php
/**
 * Author: Vitalii Pervii
 * Author URI: https://www.amconsoft.com/
 * Date: 05.07.2018
 */

namespace App\Services\Reports\Provider;


use App\Models\User;
use App\Repositories\OrderRepository;
use App\Services\Reports\FromQuery;
use Illuminate\Support\Collection;

class ReportGoods implements FromQuery
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var OrderRepository
     */
    protected $repository;

    /**
     * FromQuery constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user       = $user;
        $this->repository = app()->make('App\Repositories\OrderRepository');
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $data   = array();
        $orders = $this->selectOrders();
        // Data is creating
        foreach ($orders as $order) {
            foreach ($order->goods as $g) {
                if (!key_exists($g->id, $data)) {
                    $data[$g->id]['name']         = $g->name;
                    $data[$g->id]['quantity'] = 0;
                }

                $data[$g->id]['customers'][]  = $order->customer_id;
                $data[$g->id]['quantity'] += $g->pivot->quantity;
            }
        }
        // To count unique customers
        foreach ($data as &$item) {
            $item['count_customers'] = count(array_unique($item['customers']));
            unset($item['customers']);
        }

        return new Collection($data);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function selectOrders()
    {
        return $this->repository->query()
            ->where('provider_id', $this->user->id)
            ->with('goods')->get();
    }
}