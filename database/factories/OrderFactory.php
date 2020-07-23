<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CouponCode;
use App\Models\Order;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    // get a random user
    $user = User::query()->inRandomOrder()->first();
    // get a random address of that user
    $address = $user->addresses()->inRandomOrder()->first();
    // 10% chance that the order is refunded
    $refund = random_int(0, 10) < 1;
    // random ship status
    $ship = $faker->randomElement(array_keys(Order::$shipStatusMap));
    // coupon
    $coupon = null;
    // 30% chance that a coupon is applied to the order
    if (random_int(0, 10) < 3) {
        //only get the coupon that doesn't need min amount
        $coupon = CouponCode::query()->where('min_amount', 0)->inRandomOrder()->first();
        // increase used coupon number
        $coupon->changeUsed();
    }

    return [
        'address'        => [
            'address'       => $address->full_address,
            'postcode'           => $address->postcode,
            'contact_name'  => $address->contact_name,
            'contact_phone' => $address->contact_phone,
        ],
        'total_amount'   => 0, 
        'remark'         => $faker->sentence,
        'paid_at'        => $faker->dateTimeBetween('-30 days'), 
        'payment_method' => 'Card',
        'payment_no'     => $faker->uuid,
        'refund_status'  => $refund ? Order::REFUND_STATUS_SUCCESS : Order::REFUND_STATUS_PENDING,
        'refund_no'      => $refund ? Order::getAvailableRefundNo() : null,
        'closed'         => false,
        'reviewed'       => random_int(0, 10) > 2,
        'ship_status'    => $ship,
        'ship_data'      => $ship === Order::SHIP_STATUS_PENDING ? null : [
            'express_company' => $faker->company,
            'express_no'      => $faker->uuid,
        ],
        'extra'          => $refund ? ['refund_reason' => $faker->sentence] : [],
        'user_id'        => $user->id,
        'coupon_code_id' => $coupon ? $coupon->id : null,
    ];
});