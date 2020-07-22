@extends('layouts.app')
@section('title', 'Order Details')

@section('content')
<div class="row checkout-index-page">
  <div class="col-md-4 order-md-2 mb-4">
    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span class="text-muted">Your cart</span>
    <span class="badge badge-secondary badge-pill">{{count($order->items)}}</span>
    </h4>
    <ul class="list-group mb-3">
      @foreach($order->items as $index => $item)
     
      <li class="list-group-item d-flex justify-content-between lh-condensed">
        
        <div>
          <h6 class="my-0">{{ $item->product->title }} - {{ $item->productSku->title }}</h6>
          <small class="text-muted">A${{ $item->price }} * {{ $item->amount }}</small>
          
        </div>
        <span class="text-muted "> A${{ number_format($item->price * $item->amount, 2, '.', '') }} </span>
       
      </li>
        @if(!empty($hasPromoCode))
          <li class="list-group-item d-flex justify-content-between bg-light">
            <div class="text-success">
              <h6 class="my-0">Promo code</h6>
              <small>EXAMPLECODE</small>
            </div>
            <span class="text-success">-$5</span>
          </li>
        @endif
      @endforeach
   
      
      <li class="list-group-item d-flex justify-content-between">
        <span>Total</span>
        <strong>A${{ $order->total_amount }}</strong>
      </li>
    </ul>

    <form class="card p-2">
      <div class="input-group">
        <input type="text" class="form-control" placeholder="Promo code">
        <div class="input-group-append">
          <button type="submit" class="btn btn-secondary">Redeem</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-8 order-md-1">
    <form id="payment-form" class="needs-validation" novalidate="" action="{{route('checkout.bycard', ['order' => $order->id])}}" method="post">
      {{ csrf_field()}}
      <h4 class="mb-3">Payment</h4>
      <div class="d-block my-3">
        <div class="custom-control custom-radio">
          <input id="credit" name="paymentMethod" type="radio" class="custom-control-input" checked="" required="">
          <label class="custom-control-label" for="credit">Credit card</label>
          <div class="icon-container">
            <i class="fa fa-cc-visa" style="color:navy;"></i>
            <i class="fa fa-cc-amex" style="color:blue;"></i>
            <i class="fa fa-cc-mastercard" style="color:red;"></i>
            {{-- <i class="fa fa-cc-discover" style="color:orange;"></i> --}}
          </div>
        </div>
      </div>
      <div id="card-element"><!--Stripe.js injects the Card Element--></div>
      <button id="submit">
        <div class="spinner hidden" id="spinner"></div>
        <span id="button-text">Pay</span>
      </button>
      <p id="card-error" role="alert"></p>
      <p class="result-message hidden">
        Payment succeeded, see the result in your
        <a href="" target="_blank">Stripe dashboard.</a> Refresh the page to pay again.
      </p>
    </form>
  </div>
</div>
@endsection

@section('stripeJs')
    <script>
      (function () {
        
      // A reference to Stripe.js initialized with your real test publishable API key.
      var stripe = Stripe("{{config('stripe.public_key')}}");

      // Disable the button until we have Stripe set up on the page
      //document.querySelector("#submit").disabled = true;
      fetch("{{route('checkout.bycard', ['order'=> $order->id])}}", {
        method: "POST",
        headers: {
          'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
        }
       
      })
        .then(function(result) {
          return result.json();
        })
        .then(function(data) {
         
          var elements = stripe.elements();
          var style = {
            base: {
              color: "#32325d",
              fontFamily: 'Arial, sans-serif',
              fontSmoothing: "antialiased",
              fontSize: "16px",
              "::placeholder": {
                color: "#32325d"
              }
            },
            invalid: {
              fontFamily: 'Arial, sans-serif',
              color: "#fa755a",
              iconColor: "#fa755a"
            }
          };
          var card = elements.create("card", { style: style });
          // Stripe injects an iframe into the DOM
          card.mount("#card-element");
          card.on("change", function (event) {
            // Disable the Pay button if there are no card details in the Element
            document.querySelector("button").disabled = event.empty;
            document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
          });
          var form = document.getElementById("payment-form");
          form.addEventListener("submit", function(event) {
            event.preventDefault();
            // Complete payment when the submit button is clicked
            payWithCard(stripe, card, data.clientSecret);
          });
        });
      // Calls stripe.confirmCardPayment
      // If the card requires authentication Stripe shows a pop-up modal to
      // prompt the user to enter authentication details without leaving your page.
      var payWithCard = function(stripe, card, clientSecret) {
        loading(true);
        stripe
          .confirmCardPayment(clientSecret, {
            payment_method: {
              card: card
            }
          })
          .then(function(result) {
            if (result.error) {
              // Show error to your customer
              showError(result.error.message);
            } else {
              // The payment succeeded!
              orderComplete(result.paymentIntent.id);
            }
          });
      };
      /* ------- UI helpers ------- */
      // Shows a success message when the payment is complete
      var orderComplete = function(paymentIntentId) {
        loading(false);
        document
          .querySelector(".result-message a")
          .setAttribute(
            "href",
            "https://dashboard.stripe.com/test/payments/" + paymentIntentId
          );
        document.querySelector(".result-message").classList.remove("hidden");
        document.querySelector("#submit").disabled = true;

        // show success alert
        swal('Order Paid', '', 'success')
                    .then(function () {
                      location.href = '/orders/' + {{$order->id}};
                    });
      };
      // Show the customer the error from Stripe if their card fails to charge
      var showError = function(errorMsgText) {
        loading(false);
        var errorMsg = document.querySelector("#card-error");
        errorMsg.textContent = errorMsgText;
        setTimeout(function() {
          errorMsg.textContent = "";
        }, 4000);
      };
      // Show a spinner on payment submission
      var loading = function(isLoading) {
        if (isLoading) {
          // Disable the button and show a spinner
          document.querySelector("button").disabled = true;
          document.querySelector("#spinner").classList.remove("hidden");
          document.querySelector("#button-text").classList.add("hidden");
        } else {
          document.querySelector("button").disabled = false;
          document.querySelector("#spinner").classList.add("hidden");
          document.querySelector("#button-text").classList.remove("hidden");
        }
      };
      })();
     
      

    </script>
@endsection