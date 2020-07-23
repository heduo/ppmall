<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    public function run()
    {
        $faker = app(Faker\Generator::class);
        // fake 100 orders
        $orders = factory(Order::class, 100)->create();
        // 
        $products = collect([]);
        foreach ($orders as $order) {
            // each order has 1 to 3 items
            $items = factory(OrderItem::class, random_int(1, 3))->create([
                'order_id'    => $order->id,
                'rating'      => $order->reviewed ? random_int(1, 5) : null,  
                'review'      => $order->reviewed ? $faker->sentence : null,
                'reviewed_at' => $order->reviewed ? $faker->dateTimeBetween($order->paid_at) : null, // review time should be after paid time
            ]);

            // calc total
            $total = $items->sum(function (OrderItem $item) {
                return $item->price * $item->amount;
            });

            //if has coupon, 
            if ($order->couponCode) {
                $total = $order->couponCode->getDiscountedPrice($total);
            }

            // update total amount
            $order->update([
                'total_amount' => $total,
            ]);

            // merge items' products to products array
            $products = $products->merge($items->pluck('product'));
        }

        // filter out duplicated product id
        $products->unique('id')->each(function (Product $product) {
            
            $result = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', function ($query) {
                    $query->whereNotNull('paid_at');
                })
                ->first([
                    \DB::raw('count(*) as review_count'), // review count
                    \DB::raw('avg(rating) as rating'), // avg rating
                    \DB::raw('sum(amount) as sold_count'), // qty sold
                ]);

            $product->update([
                'rating'       => $result->rating ?: 5, // defaut rating is 5
                'review_count' => $result->review_count,
                'sold_count'   => $result->sold_count,
            ]);
        });
    }
}