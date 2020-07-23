<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\CouponCode;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use Carbon\Carbon;
use App\Exceptions\CouponCodeUnavailableException;

class OrderService
{
    public function store(User $user, UserAddress $address, $remark, $items, CouponCode $coupon = null)
    {
        if ($coupon) {
            $coupon->checkAvailable($user);
        }
         // start a DB transaction
        $order = \DB::transaction(function () use ($user, $address, $remark, $items, $coupon) {
            // update address's last_used_at time
            $address->update(['last_used_at' => Carbon::now()]);
            // create a new order
            $order   = new Order([
                'address'      => [ 
                    'address'       => $address->full_address,
                    'postcode'           => $address->postcode,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark'       => $remark,
                'total_amount' => 0,
            ]);
           // associate with user model
            $order->user()->associate($user);
            // write to table
            $order->save();

            $totalAmount = 0;
            // for every item
            foreach ($items as $data) {
                $sku  = ProductSku::find($data['sku_id']);
                 // Create OrderItem model and associate with Order model
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price'  => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount += $sku->price * $data['amount'];

                // check stock by decrease stock, deceaseStock return number of affected rows 
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new InvalidRequestException('Stock is insufficient');
                }
            }

            // apply coupon code
            if ($coupon) {
                $coupon->checkAvailable($user, $totalAmount);
                $totalAmount = $coupon->getDiscountedPrice($totalAmount);
                $order->couponCode()->associate($coupon);
                
                if ($coupon->changedUsed()<=0) {
                   throw new CouponCodeUnavailableException('This coupon code is used out');
                }

            }
            // update total amount
            $order->update(['total_amount' => $totalAmount]);

            /// remove items from cart
            $skuIds = collect($items)->pluck('sku_id')->all();
            app(CartService::class)->remove($skuIds);

            return $order;
        });

        // dispatch job
        dispatch(new CloseOrder($order, config('app.order_expire')));

        return $order;
    }
}