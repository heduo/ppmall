<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Notifications\OrderPaidNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

// implements ShouldQueue 代表异步监听器
class SendOrderPaidMail implements ShouldQueue
{
    public function handle(OrderPaid $event)
    {
        // get order from event
        $order = $event->getOrder();
        // use notify method ot send notification
        $order->user->notify(new OrderPaidNotification($order));
    }
}