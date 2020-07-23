<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Support\Str;
use App\Exceptions\CouponCodeUnavailableException;
use Carbon\Carbon;
use App\Models\User;

class CouponCode extends Model
{
    use DefaultDatetimeFormat;
    // coupon types constants
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENT = 'percent';

    public static $typeMap = [
        self::TYPE_FIXED   => 'Amount',
        self::TYPE_PERCENT => 'Percent',
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'total',
        'used',
        'min_amount',
        'not_before',
        'not_after',
        'enabled',
    ];
    protected $casts = [
        'enabled' => 'boolean',
    ];
    
    protected $dates = ['not_before', 'not_after'];

    protected $appends = ['description'];

    public static function findAvailableCode($length = 8)
    {
        do {
            // generate a ramdom coupon code
            $code = strtoupper(Str::random($length));
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }

    public function getDescriptionAttribute()
    {
        $str = '';

        if ($this->min_amount>0) {
            $str = ' on Orders Over A$'.str_replace('.00', '', $this->min_amount);
        }

        if ($this->type === self::TYPE_PERCENT) {
            return str_replace('.00', '', $this->value).'% Off'.$str;
        }

        return 'A$'.str_replace('.00', '', $this->value).' Off'.$str;
    }

    public function checkAvailable(User $user, $orderAmount = null)
    {
        

        // if code is not enabled
        if (!$this->enabled) {
            throw new CouponCodeUnavailableException('This code does not exist');
        }

        // if code is used out
        if ($this->total - $this->used <= 0) {
            throw new CouponCodeUnavailableException('This code is used out');
        }

        if ($this->not_before && $this->not_before->gt(Carbon::now())) {
            throw new CouponCodeUnavailableException('This code is not available yet');
        }

        if ($this->not_after && $this->not_after->lt(Carbon::now())) {
            throw new CouponCodeUnavailableException('This code has expired');
        }

        if (!is_null($orderAmount) && $orderAmount < $this->min_amount) {
            throw new CouponCodeUnavailableException('Amount is too small to use this code');
        }

        // if order is paid and not refunded, then coupon is used; if order is not paid and order is not closed, then coupon is used
        /**
         * 
         * select * from orders where user_id = xx and coupon_code_id = xx
         * and (
         * ( paid_at is null and closed = 0 )
         * or ( paid_at is not null and refund_status != 'success' )
         * )
         * 
         */
        $used = Order::where('user_id', $user->id)
                ->where('coupon_code_id', $this->id)
                ->where(function ($query){
                   $query->where(function ($query){
                       $query->whereNull('paid_at')->where('closed', false);
                   })->orWhere(function ($query){
                    $query->whereNotNull('paid_at')->where('refund_status', '!=', Order::REFUND_STATUS_SUCCESS);
                }); 
            })->exists();

        if ($used) {
           throw new CouponCodeUnavailableException("You've used this coupon code");
           
        }
    }

    public function getDiscountedPrice($orderAmount)
    {
       if ($this->type === self::TYPE_FIXED) {
           // min amount should greater than 0.01
           return max(0.01, $orderAmount - $this->value);
       }

       return number_format($orderAmount * (100-$this->value)/100, 2, '.', '');
    }

    public function changeUsed($increase = true)
    {
        if ($increase) {
            // increase used, if it's less than total
            return $this->where('id', $this->id)->where('used', '<', $this->total)->increment('used'); 
        }else{
            return $this->decrement('used'); // if order expires, decrease used
        }
    }
}