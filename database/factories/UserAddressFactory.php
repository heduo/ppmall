<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\UserAddress;
use Faker\Generator as Faker;

$factory->define(UserAddress::class, function (Faker $faker) {
    $addresses = [
        ["NSW", "Sydney"],
        ["VIC", "Melbourne"],
        ["QLD", "Brisbane"],
        ["TAS", "Hobart"],
        ["WA", "Perth"],
        ["SA". "Adelaide"],
        ["ACT", "Canberra"],
        ["NT", "Darwin"],
        ["ACT", "Jevis Bay Village"]
    ];

    $address = $faker->randomElement($addresses);

    if(count($address)==2){
        $state = $address[0];
        $suburb = $address[1];
    }else{
        $state = 'SA';
        $suburb = 'Adelaide';
    }
    return [
        'country' => 'Australia',
        'suburb' => $suburb,
        'state' => $state,
        'address1' => sprintf('%d %s St', $faker->randomNumber(2), $faker->firstName()),
        'address2' => '',
        'postcode' => $faker->randomNumber(4),
        'contact_name' => $faker->name,
        'contact_phone' => $faker->randomNumber()
    ];
   
});
