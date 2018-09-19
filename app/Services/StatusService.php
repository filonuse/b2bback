<?php

namespace App\Services;


use App\Enums\GoodsAction;
use App\Enums\OrderStatus;
use App\Models\Status;
use App\Repositories\StatusRepository;
use Illuminate\Database\Eloquent\Model;

class StatusService
{
    /**
     * @var Status
     */
    protected $status;
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var StatusRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $handler;

    /**
     * StatusService constructor.
     * @param Model $model
     * @throws \Exception
     */
    public function __construct(Model $model)
    {
        $this->handler = 'sync' . class_basename($model);

        if (!method_exists($this, $this->handler))
            abort(405, 'Method not allowed');

        $this->model      = $model;
        $this->repository = app(StatusRepository::class);
    }

    /**
     * @param int|string $status the status id or name
     */
    protected function setStatus($status)
    {
        $this->status = $this->repository->query()
            ->when(is_numeric($status), function ($query) use ($status) {
                return $query->where('id', $status);
            }, function ($query) use ($status) {
                return $query->where('name', $status);
            })->first();
    }

    /**
     * @param int|string $status Status id or name
     * @return bool
     *
     * @throws \Exception
     */
    public function save($status)
    {
        $this->setStatus($status);
        $this->{$this->handler}();

        return true;
    }

    /*
     | -------------------------------------------------------------------------
     |      Order
     | -------------------------------------------------------------------------
     */

    /**
     * Change status the specified order in storage
     *
     * @throws \Exception
     */
    protected function syncOrder()
    {
        $this->model->load(['goods', 'status']);

        \DB::beginTransaction();

        try {
            foreach ($this->model->goods as $goods) {
                switch ($this->status->name) {
                    case OrderStatus::PENDING:
                        if ($this->model->status === null) {
                            GoodsService::syncQuantity($goods, GoodsAction::RESERVE, $goods->pivot->quantity);
                        }
                        break;

                    case OrderStatus::ACCEPTED_CUSTOMER:
                        GoodsService::syncQuantity($goods, GoodsAction::WRITE_OFF, $goods->pivot->quantity);
                        break;

                    case OrderStatus::CANCELED:
                        GoodsService::syncQuantity($goods, GoodsAction::REVERT, $goods->pivot->quantity);
                        break;
                }
            }

            $this->model->update(['current_status_id' => $this->status->id]);

            \App\Models\Relationships\OrderStatus::query()->create([
                'order_id'  => $this->model->id,
                'status_id' => $this->status->id,
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            abort(404);
        }
    }
}