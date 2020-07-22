<?php

namespace App\Services\Payments;

use Auth;
use App\Models\Order;


class StripeService
{
  public function __construct() {
    \Stripe\Stripe::setApiKey(config('stripe.secret_key'));
  }
  // generate client secret
  public function createClientSecret($amount, $metadata, $currency = 'aud')
  {
    try {
    
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

  public function refund($chargeId, $amount=null)
  {
    return \Stripe\Refund::create([
      'amount' => $amount,
      'charge' => $chargeId
    ]);
  }

}
