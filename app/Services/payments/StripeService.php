<?php
namespace App\Services\Payments;

use Auth;
use App\Models\Order;


class StripeService
{
    // generate client secret
    public function createClientSecret($secretKey, $amount, $metadata,$currency='aud')
    {
        try {
            // This is your real test secret API key.
            \Stripe\Stripe::setApiKey($secretKey);
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amount,
                'currency' => $currency,
                'metadata' => $metadata
              ]);
            
            $output = [
            'clientSecret' => $paymentIntent->client_secret,
            ];
            
            return $output;

        } catch (\Error $e) {
          http_response_code(500);
          return ['error' => $e->getMessage()];
        }
    }

}

