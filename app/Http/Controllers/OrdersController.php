<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Models\Order;
use Carbon\Carbon;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use App\Services\CartService;
use App\Services\OrderService;
use App\Http\Requests\SendReviewRequest;

class OrdersController extends Controller
{
    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user  = $request->user();
        $address = UserAddress::find($request->input('address_id'));

        return $orderService->store($user, $address, $request->input('remark'), $request->input('items'));
        
    }

    public function index(Request $request)
    {
        $orders = Order::query()
            // eager loading with with method, avoiding N+1 problem
            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('orders.index', ['orders' => $orders]);
    }

    public function show(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        return view('orders.show', ['order'=> $order->load(['items.productSku', 'items.product'])]);
    }

    public function received(Order $order, Request $request)
    {
        $this->authorize('own', $order);

        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('Incorrect Shipping Status');
            
        }

        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        return $order;
    }

    public function review(Order $order)
    {
        // check access right
        $this->authorize('own', $order);

        // check if order paid
        if (!$order->paid_at) {
            throw new InvalidRequestException('This order is not paid yet for review');
        }

        return view('orders.review', ['order'=>$order->load(['items.productSku', 'items.product'])]);
    }

    public function sendReview(Order $order, SendReviewRequest $request)
    {
        $this->authorize('own', $order);
        if (!$order->paid_at) {
            throw new InvalidRequestException("This order hasn't been paid for rating");    
        }

        if ($order->review) {
            throw new InvalidRequestException("This order is rated");
        }

        $reviews = $request->input('reviews');

        // start transaction
        \DB::transaction(function () use ($reviews, $order)
        {
           foreach ($reviews as $review) {
               $orderItem = $order->items()->find($review['id']);
               // udpate orderItems
               $orderItem->update([
                   'rating' => $review['rating'],
                   'review' => $review['review'],
                   'reviewed_at' => Carbon::now()
               ]);
           }
           // update orders table
           $order->update(['reviewed' => true]);

           // trigger event to update Product review count and rating
           event(new OrderReviewed($order));
        });

        return redirect()->back();
    }
}