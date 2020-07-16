<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Yansongda\Pay\Pay as QrCodePay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // register alipay service
        $this->app->singleton('alipay', function ()
        {
            $config = config('pay.alipay');

            if (app()->environment()!=='production') {
                $config['mode'] = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            }else{
                $config['log']['level'] = Logger::WARNING;
            }

            return QrCodePay::alipay($config);
        });

        // register wechat pay service
        $this->app->singleton('wechat_pay', function ()
        {
            $config = config('pay.wechat');

            if (app()->environment()!=='production') {
                $config['mode'] = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            }else{
                $config['log']['level'] = Logger::WARNING;
            }

            return QrCodePay::wechat($config);
        
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
