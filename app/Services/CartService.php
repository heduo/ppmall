<?php

namespace App\Services;

use Auth;
use App\Models\CartItem;

class CartService
{
    public function get()
    {
        return Auth::user()->cartItems()->with(['productSku.product'])->get();
    }

    public function add($skuId, $amount)
    {
        $user = Auth::user();
        //check if item exist, update amount
        if ($item = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
            
            $item->update([
                'amount' => $item->amount + $amount,
            ]);
        } else {
            // if not exist, then insert new item
            $item = new CartItem(['amount' => $amount]);
            $item->user()->associate($user);
            $item->productSku()->associate($skuId);
            $item->save();
        }

        return $item;
    }

    public function remove($skuIds)
    {
        // if only one skuId passed in, then convert it to an array contains only itself
        if (!is_array($skuIds)) {
            $skuIds = [$skuIds];
        }
        Auth::user()->cartItems()->whereIn('product_sku_id', $skuIds)->delete();
    }
}