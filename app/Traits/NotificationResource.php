<?php

namespace App\Traits;


use Illuminate\Support\Collection;

trait NotificationResource
{

    /**
     * @var Collection
     */
    protected $resource;

    /**
     * @param string $action
     * @param int $actionId
     * @param int $fromUserId
     * @param $value
     */
    public function createData(string $action, int $actionId, int $fromUserId, $value = null)
    {
        $this->resource = new Collection([
            'action_id'    => $actionId,
            'action'       => $action,
            'from_user_id' => $fromUserId,
            'value'        => $value,
        ]);
    }

    /**
     * @return Collection
     */
    public function getData()
    {
        return $this->resource;
    }
}