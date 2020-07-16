<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Models\Order;
use Carbon\Carbon;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;

class OrdersController extends Controller
{
    public function store(OrderRequest $request)
    {
        $user  = $request->user();
        // start a DB transaction
        $order = \DB::transaction(function () use ($user, $request) {
            $address = UserAddress::find($request->input('address_id'));
            // update address's last_used_at time
            $address->update(['last_used_at' => Carbon::now()]);
            // create a new order
            $order   = new Order([
                'address'      => [ // add address info
                                    'address'       => $address->full_address,
                                    'postcode'           => $address->postcode,
                                    'contact_name'  => $address->contact_name,
                                    'contact_phone' => $address->contact_phone,
                ],
                'remark'       => $request->input('remark'),
                'total_amount' => 0,
            ]);
            // associate with user model
            $order->user()->associate($user);
            // write to table
            $order->save();

            $totalAmount = 0;
            $items       = $request->input('items'); 
            // 
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
                if ($sku->decreaseStock($data['amount'])<=0) {
                   throw new InvalidRequestException("Error Processing Request", 1);
                   
                }
            }

            // update total amount
            $order->update(['total_amount' => $totalAmount]);

            // remove items from cart
            $skuIds = collect($items)->pluck('sku_id');
            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();;

            return $order;
        });

        $this->dispatch(new CloseOrder($order, config('app.order_expire')));
        return $order;
    }
}