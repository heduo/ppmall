<?php

use App\Models\UserAddress;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::all()->each(function (User $user)
        {
            factory(UserAddress::class, random_int(1,3))->create(['user_id' => $user->id]);
        });
    }
}
