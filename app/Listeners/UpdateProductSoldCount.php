<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\OrderItem;
//  implements ShouldQueue 代表此监听器是异步执行的
class UpdateProductSoldCount implements ShouldQueue
{
    public function handle(OrderPaid $event)
    {
        // get order from event
        $order = $event->getOrder();
        // preload product info
        $order->load('items.product');
        
        foreach ($order->items as $item) {
            $product   = $item->product;
            // count sold quantity
            $soldCount = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', function ($query) {
                    $query->whereNotNull('paid_at');  // Item is paid
                })->sum('amount');
            // update sold_count
            $product->update([
                'sold_count' => $soldCount,
            ]);
        }
    }
}