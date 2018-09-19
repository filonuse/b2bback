<?php

namespace App\Listeners\Order;

use App\Enums\SettingType;
use App\Events\Order\OrderStatusUpdated;
use App\Notifications\Order\OrderStatusUpdated as OrderStatusNotify;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderStatusUpdatedNotification
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
     * @param  OrderStatusUpdated  $event
     * @return void
     */
    public function handle(OrderStatusUpdated $event)
    {
        if ($user = $event->order->customer()->first()) {
            $setting = $user->settings->where('name', SettingType::NOTIFY_STATUS_UPDATED)->first();

            if ($setting->isUserNotify()) {
                $user->notify(new OrderStatusNotify($event->getData()));
            }
        }
    }
}
