@extends('layouts.app')
@section('title', 'Order Details')

@section('content')
<div class="row">
  <div class="col-lg-10 offset-lg-1">
    <div class="card">
    <div class="card-header">
      <h4>Order Details</h4>
    </div>
    <div class="card-body">
      <table class="table">
        <thead>
        <tr>
          <th>Product</th>
          <th class="text-center">Price</th>
          <th class="text-center">Quantity</th>
          <th class="text-right item-amount">Subtotal</th>
        </tr>
        </thead>
        @foreach($order->items as $index => $item)
          <tr>
            <td class="product-info">
              <div class="preview">
                <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">
                  <img src="{{ $item->product->image_url }}">
                </a>
              </div>
              <div>
                <span class="product-title">
                  <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
                </span>
                <span class="sku-title">{{ $item->productSku->title }}</span>
              </div>
            </td>
            <td class="sku-price text-center vertical-middle">A${{ $item->price }}</td>
            <td class="sku-amount text-center vertical-middle">{{ $item->amount }}</td>
            <td class="item-amount text-right vertical-middle">A${{ number_format($item->price * $item->amount, 2, '.', '') }}</td>
          </tr>
        @endforeach
        <tr><td colspan="4"></td></tr>
      </table>
      <div class="order-bottom">
        <div class="order-info">
          <div class="line"><div class="line-label">Address：</div><div class="line-value">{{ join(' ', $order->address)}}</div></div>
          <div class="line"><div class="line-label">Remark：</div><div class="line-value">{{ $order->remark ?: '-' }}</div></div>
          <div class="line"><div class="line-label">Order No.：</div><div class="line-value">{{ $order->no }}</div></div>
          <div class="line"><div class="line-lable">Shipping Status:</div><div class="line-value">{{\App\Models\Order::$shipStatusMap[$order->ship_status]}}</div></div>
          <!-- ship info -->
          @if ($order->ship_data)
              <div class="line">
                <div class="line-lable">Shipping Info</div>
                <div class="line-value">{{$order->ship_data['express_company']}} {{$order->ship_data['express_no']}}</div>
              </div>
          @endif
          @if ($order->paid_at && $order->refund_status !== \App\Models\Order::REFUND_STATUS_PENDING)
              <div class="line">
                <div class="line-label">
                  Refund Status: 
                </div>
                <div class="line-value">{{ \App\Models\Order::$refundStatusMap[$order->refund_status]}}</div>
              </div>
              <div class="line">
                <div class="line-label">Refund Reasons: </div>
              <div class="line-value">{{ $order->extra['refund_reason']}}</div>
              </div>
          @endif
        </div>
        <div class="order-summary text-right">
          <div class="total-amount">
            <span>Total Amount：</span>
            <div class="value">A${{ $order->total_amount }}</div>
          </div>
          <div>
            <span>Status：</span>
            <div class="value">
              @if($order->paid_at)
                @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                  Paid
                @else
                  {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
                @endif
              @elseif($order->closed)
                Closed
              @else
                Unpaid
              @endif
            </div>
          </div>
          @if (!$order->paid_at && !$order->closed)
              <div class="payment-buttons">
                <a class="btn btn-primary btn-sm" href="{{route('checkout.index', ['order'=>$order->id])}}">Continue to Check Out</a>
              </div>
          @endif
          @if ($order->ship_status === \App\Models\Order::SHIP_STATUS_DELIVERED)
              <div class="receive-button">
                <button type="button" id="btn-receive" class="btn btn-sm btn-success">Confirm Receipt </button>
              </div>
          @endif

          @if ($order->paid_at && $order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
              <div class="refund-button">
                <button class="btn btn-sm btn-danger" id="btn-apply-refund">Apply Refund</button>
              </div>
          @endif
          
        </div>
      </div>
    </div>
    </div>
  </div>
</div>
@endsection
@section('scriptsAfterJs')
    <script>
      $(document).ready(function(){
        // confirm order received
        $('#btn-receive').click(function(){
          swal({
            title: "Are you sure you've received your goods ?",
            icon: "warning",
            dangerMode: true,
            buttons: ["Cancel", "Confirm received"]
          })
          .then(function(res){
            // if user click 'Cancel'
            if (!res) {
              return;
            }

            axios.post('{{ route('orders.received', [$order->id])}}')
            .then(function () {
              location.reload();
            });

          });
        });

        // apply for refund
        $('#btn-apply-refund').click(function () {
          swal({
            text: 'Please input refund reasons',
            content: 'input'
          }).then(function (input) {
            if (!input) {
              swal('Refund reasons are required', '', 'error'); return;
            }

            axios.post('{{route('orders.apply_refund', [$order->id])}}', {reason:input})
            .then(function () {
              swal('Apply for refund successfully', '', 'success').then(function () {
                location.reload();
              });
            });
          });
        });
      });
    </script>
@endsection

