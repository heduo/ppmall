<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CouponCode;
use Faker\Generator as Faker;

$factory->define(CouponCode::class, function (Faker $faker) {
    // first get a ramdom type
    $type  = $faker->randomElement(array_keys(CouponCode::$typeMap));
    // generate ramdom value for a ramdom type
    $value = $type === CouponCode::TYPE_FIXED ? random_int(1, 200) : random_int(1, 50);

    // if type is fixed amount 
    if ($type === CouponCode::TYPE_FIXED) {
        $minAmount = $value + 0.01;
    } else {
        // if type is percentage, then 50% chance that minAmount is not required
        if (random_int(0, 100) < 50) {
            $minAmount = 0;
        } else {
            $minAmount = random_int(100, 1000); // otherwise, the minAmount is a ramdom int from 100 to 1000
        }
    }

    return [
        'name'       => join(' ', $faker->words), 
        'code'       => CouponCode::findAvailableCode(), 
        'type'       => $type,
        'value'      => $value,
        'total'      => 1000,
        'used'       => 0,
        'min_amount' => $minAmount,
        'not_before' => null,
        'not_after'  => null,
        'enabled'    => true,
    ];
});