<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Order;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $seconds)
    {
        $this->order = $order;
        $this->delay($seconds);

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // if order is paid
        if ($this->order->paid_at) {
           return;
        }

        // close order using transaction
        \DB::transaction(function ()
        {
            // set closed to true
            $this->order->update(['closed' => true]);

            // add quantity back to stock for SKUs
            foreach ($this->order->items  as $item) {
                $item->productSku -> addStock($item->amount);
            }

            if ($this->order->couponCode) {
                $this->order->couponCode->changeUsed(false);
            }
        });
    }
}
