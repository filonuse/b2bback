<?php

namespace App\Listeners\Order;

use App\Enums\SettingType;
use App\Events\Order\OrderCreated;
use App\Notifications\Order\OrderCreated as OrderCreatedNotify;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendOrderCreatedNotification
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
     * @param  OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {
        if ($user = $event->order->provider()->with('settings')->first()) {

            $setting = $user->settings->where('name', SettingType::NOTIFY_ORDER_CREATED)->first();

            if ($setting->isUserNotify()) {
                $user->notify(new OrderCreatedNotify($event->getData()));
            }
        }
    }
}
