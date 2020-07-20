<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Order;

class OrderPaidNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Order Paid Successfully')  
            ->greeting('Hi '.$this->order->user->name) 
            ->line('Your order is successfully paid at '.$this->order->created_at->format('m-d H:i')) 
            ->action('View your order', route('orders.show', [$this->order->id])) //view order link
            ->success(); // button color
    }
}