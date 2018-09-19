<?php

namespace App\Events;

use App\Enums\NotificationActionType;
use App\Models\Goods;
use App\Models\Review;
use App\Models\User;
use App\Traits\NotificationResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReviewCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, NotificationResource;

    /**
     * @var User
     */
    public $user;

    /**
     * @var Review
     */
    public $review;

    /**
     * @var Review
     */
    public $action;

    /**
     * Create a new event instance.
     *
     * @param Review $review
     * @return void
     */
    public function __construct(Review $review)
    {
        if ($review->reviewable_type === Goods::class) {
            $this->action = NotificationActionType::REVIEW_GOODS;
            $this->user   = Goods::find($review->reviewable_id)->user()->first();
        } elseif ($review->reviewable_type === User::class) {
            $this->action = NotificationActionType::REVIEW_USER;
            $this->user   = User::find($review->reviewable_id);
        }

        $this->createData($this->action, $review->id, $review->from_user_id, $review->estimate);

        $this->review = $review;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.User.' . $this->user->id);
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
        return $this->action;
    }
}
