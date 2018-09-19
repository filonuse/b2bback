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

class ReportCustomers implements FromQuery
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
            $key = $order->customer->id;

            if (!key_exists($key, $data)) {
                $data[$key]['name']           = $order->customer->name;
                $data[$key]['legal_name']     = $order->customer->legal_name;
                $data[$key]['count_supplies'] = 0;
                $data[$key]['amount']         = 0;
            }

            ++$data[$key]['count_supplies'];

            foreach ($order->goods as $g) {
                $data[$key]['goods'][] = $g->id;
                $data[$key]['amount']  += ($g->pivot->price * $g->pivot->quantity);
            }
        }
        // To count unique goods
        foreach ($data as &$item) {
            $item['avg_amount']         = ($item['amount'] / $item['count_supplies']);
            $item['count_nomenclature'] = count(array_unique($item['goods']));
            unset($item['goods']);
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
            ->with(['customer', 'goods'])->get();
    }
}