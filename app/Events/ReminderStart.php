<?php

namespace App\Events;

use App\Enums\NotificationActionType;
use App\Models\Reminder;
use App\Traits\NotificationResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReminderStart implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, NotificationResource;

    /**
     * @var Reminder
     */
    public $reminder;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Reminder $reminder)
    {
        $this->reminder = $reminder;

        $this->createData(
            NotificationActionType::REMINDER,
            $this->reminder->id,
            $this->reminder->user_id,
            [
                'description' => $this->reminder->description,
                'date_at'     => $this->reminder->date_at,
                'on_days'     => $this->reminder->on_days,
            ]
        );
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.User.' . $this->reminder->user_id);
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
        return NotificationActionType::REMINDER;
    }
}
