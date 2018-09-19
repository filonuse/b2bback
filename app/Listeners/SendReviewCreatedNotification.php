<?php

namespace App\Listeners;

use App\Enums\SettingType;
use App\Events\ReviewCreated;
use App\Notifications\ReviewCreatedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendReviewCreatedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ReviewCreated  $event
     * @return void
     */
    public function handle(ReviewCreated $event)
    {
        if ($event->user) {
            $setting = $event->user->settings->where('name', SettingType::NOTIFY_REVIEW_CREATED)->first();

            if ($setting->isUserNotify()) {
                $event->user->notify(new ReviewCreatedNotification($event->getData()));
            }
        }
    }
}
