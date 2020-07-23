<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OrderItem;
use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(OrderItem::class, function (Faker $faker) {
    // get a random product
    $product = Product::query()->where('on_sale', true)->inRandomOrder()->first();
    // get a random SKU
    $sku = $product->skus()->inRandomOrder()->first();

    return [
        'amount'         => random_int(1, 5), // random quantity from 1 to 5
        'price'          => $sku->price,
        'rating'         => null,
        'review'         => null,
        'reviewed_at'    => null,
        'product_id'     => $product->id,
        'product_sku_id' => $sku->id,
    ];
});