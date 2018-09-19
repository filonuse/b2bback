<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Order\OrderCreated'       => [
            'App\Listeners\Order\SendOrderCreatedNotification',
        ],
        'App\Events\Order\OrderStatusUpdated' => [
            'App\Listeners\Order\SendOrderStatusUpdatedNotification',
        ],
        'App\Events\ReviewCreated'            => [
            'App\Listeners\SendReviewCreatedNotification',
        ],
        'App\Events\ReminderStart'            => [
            'App\Listeners\SendReminderNotification',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
