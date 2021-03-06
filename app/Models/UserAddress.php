<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'country',
        'state',
        'suburb',
        'postcode',
        'address1',
        'address2',
        'contact_name',
        'contact_phone',
        'last_used_at',
    ];
    protected $dates = ['last_used_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute()
    {
        $address = $this->address2 ? $this->address2.' '.$this->address1 : $this->address1;
        return "{$address}, {$this->surburb} {$this->state} {$this->postcode}, {$this->country}";
    }
}
