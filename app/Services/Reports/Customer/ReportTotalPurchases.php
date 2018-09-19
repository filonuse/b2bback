<?php

namespace App\Services\Reports\Customer;


use App\Models\User;
use App\Repositories\OrderRepository;
use App\Services\Reports\FromQuery;
use Illuminate\Support\Collection;

class ReportTotalPurchases implements FromQuery
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
     * TotalPurchases constructor.
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
        $items['last_month']    = $this->selectOrdersLastMonth()->sum('amount');
        $items['current_month'] = $this->selectOrdersCurrentMonth()->sum('amount');
        $items['difference']    = ($items['current_month'] - $items['last_month']);

        return new Collection($items);
    }

    /**
     * @return mixed
     */
    protected function selectOrdersCurrentMonth()
    {
        return $this->repository->query()
            ->where('customer_id', $this->user->id)
            ->whereMonth('created_at', '>=', now()->month)
            ->get(['amount']);
    }

    /**
     * To select orders within the current month
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function selectOrdersLastMonth()
    {
        return $this->repository->query()
            ->where('customer_id', $this->user->id)
            ->whereMonth('created_at', '=', now()->subMonth()->month)
            ->get(['amount']);
    }
}