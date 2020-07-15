<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    const SHIP_STATUS_PENDING = 'pending';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED = 'received';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => 'Refund Pending',
        self::REFUND_STATUS_APPLIED    => 'Refund Applied',
        self::REFUND_STATUS_PROCESSING => 'Refund Processing',
        self::REFUND_STATUS_SUCCESS    => 'Refund Success',
        self::REFUND_STATUS_FAILED     => 'Refund Failed',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING   => 'Ship Pending',
        self::SHIP_STATUS_DELIVERED => 'Ship Delivered',
        self::SHIP_STATUS_RECEIVED  => 'Ship Received',
    ];

    protected $fillable = [
        'no',
        'address',
        'total_amount',
        'remark',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
        'refund_no',
        'closed',
        'reviewed',
        'ship_status',
        'ship_data',
        'extra',
    ];

    protected $casts = [
        'closed'    => 'boolean',
        'reviewed'  => 'boolean',
        'address'   => 'json',
        'ship_data' => 'json',
        'extra'     => 'json',
    ];

    protected $dates = [
        'paid_at',
    ];

    protected static function boot()
    {
        parent::boot();
        // before create a model
        static::creating(function ($model) {
            // if order 'no' is empty
            if (!$model->no) {
                // find a available order number
                $model->no = static::findAvailableNo();
                // if fail to find order no
                if (!$model->no) {
                    return false;
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function findAvailableNo()
    {
        // order number prefix
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // ramdon 6 numbers
            $no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // check if order exists
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
        }
        \Log::warning('Find order no failed');

        return false;
    }
}