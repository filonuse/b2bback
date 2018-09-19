<?php

namespace App\Events\Order;

use App\Enums\NotificationActionType;
use App\Models\Order;
use App\Models\Relationships\OrderStatus;
use App\Traits\NotificationResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OrderStatusUpdated implements  ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, NotificationResource;

    /**
     * @var Order
     */
    public $order;

    /**
     * Create a new event instance.
     *
     * @param OrderStatus $orderStatus
     * @return void
     */
    public function __construct(OrderStatus $orderStatus)
    {
        $this->order = $orderStatus->order()->first();
        $this->createData(
            NotificationActionType::ORDER_STATUS,
            $this->order->id,
            $this->order->customer_id,
            $this->order->current_status_id
        );
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.User.' . $this->order->customer_id);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return $this->resource->toArray();
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return NotificationActionType::ORDER_STATUS;
    }
}
