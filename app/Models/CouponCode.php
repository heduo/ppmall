<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Support\Str;

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
}