<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use App\Services\Payments\StripeService;
use App\Events\OrderPaid;

use Carbon\Carbon;

class CheckoutController extends Controller
{
    public function index(Order $order)
    {
         // check if order belongs to user
         $this->authorize('own', $order);


        return view('checkout.index',['order'=> $order->load(['items.productSku', 'items.product'])]);
    }

    public function payByCard(Order $order, Request $request)
    {
         // check if order belongs to user
         $this->authorize('own', $order);
        // if order paid or closed
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException("Order Status is not correct");   
        }

        $stripeService = new StripeService;

        $totalAmount = $order->total_amount*100;

       // $apiKey = "sk_test_51H6rl7J5k6agZHzcRbLRB2beYOzWy7KFEVOupkqmCu0uS4OVVXHQnTYmwbkSHFK6YyQy26kh13fAhxJHyEGmOIp000uSLMPnPn";
        $metadata = [
            'order_no' => $order->no,
            'payment_method' => 'Card'
        ];
        $res = $stripeService->createClientSecret($totalAmount, $metadata);

        return response()->json([
            'clientSecret' => $res['clientSecret']
        ]);

    }

    // notification from Stripe when payment made successfully
    public function stripeNotify()
    {
        // response 
        $json_str = file_get_contents('php://input');
        $res = json_decode($json_str, 1);
        // print_r($res['data']['object']);
        $charge = $res['data']['object'];
        
        $paid_at = date("Y-m-d H:i:s", substr($charge['created'], 0, 10));

         // if payment failed
         if(!$charge['paid']) {
             // log error
         }
    
         // if payment success
         // retrieve order
         $order_no = $charge['metadata']['order_no'];

        //  find order based on order no
         $order = Order::where('no', $order_no)->first();
        // 
         if (!$order) {
             // log error
             return false;
         }
         // if order is paid
         if ($order->paid_at) {
             
             return true;
         }
 
         $order->update([
             'paid_at'        => $paid_at, 
             'payment_method' => 'Card', 
             'payment_no'     => $charge['id'], // charge id from stripe
         ]);

         $this->afterPaid($order);
 
        return true;
    }

    public function afterPaid(Order $order)
    {
        event(new OrderPaid($order));
    }
}
