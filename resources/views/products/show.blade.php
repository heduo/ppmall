@extends('layouts.app')
@section('title', $product->title)

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-body product-info">
    <div class="row">
      <div class="col-5">
        <img class="cover" src="{{ $product->image_url }}" alt="">
      </div>
      <div class="col-7">
        <div class="title">{{ $product->title }}</div>
        <div class="price"><label>Price</label><em>A$ </em><span>{{ $product->price }}</span></div>
        <div class="sales_and_reviews">
          <div class="sold_count"><span class="count">{{ $product->sold_count }} Sold</span></div>
        <div class="review_count">Reviews <span class="count">{{ $product->review_count }}</span></a></div>
          <div class="rating" title="Rating {{ $product->rating }}">Rating <span class="count">{{ str_repeat('★', floor($product->rating)) }}{{ str_repeat('☆', 5 - floor($product->rating)) }}</span></div>
        </div>
        <div class="skus">
          <label>Select</label>
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            @foreach($product->skus as $sku)
              <label 
                class="btn sku-btn" 
                data-toggle="tooltip"
                data-price="{{$sku->price}}"
                data-stock="{{$sku->stock}}"
                title="{{ $sku->description }}" >
                <input type="radio" name="skus" autocomplete="off" value="{{ $sku->id }}"> {{ $sku->title }}
              </label>
            @endforeach
          </div>
        </div>
        <div class="cart_amount"><label>Quntity</label><input type="text" class="form-control form-control-sm" value="1"><span class="stock"></span></div>
        <div class="buttons">
          @if ($favored)
            <button class="btn btn-sm btn-danger btn-disfavor">Unsave</button>
          @else
            <button class="btn btn-sm btn-default btn-favor">❤ Save</button>
          @endif
          <button class="btn btn-sm btn-primary btn-add-to-cart">Add to Cart</button>
        </div>
      </div>
    </div>
    <div class="product-detail">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" href="#product-detail-tab" aria-controls="product-detail-tab" role="tab" data-toggle="tab" aria-selected="true">Description</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#product-reviews-tab" aria-controls="product-reviews-tab" role="tab" data-toggle="tab" aria-selected="false">Reviews</a>
        </li>
      </ul>
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="product-detail-tab">
          {!! $product->description !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="product-reviews-tab">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>User</tr>
              <tr>Product</tr>
              <tr>Rating</tr>
              <tr>Review</tr>
              <tr>Time</tr>
            </thead>
            <tbody>
              @foreach ($reviews as $review)
                  <tr>
                    <td>{{$review->order->user->name}}</td>
                    <td>{{$review->productSku->title}}</td>
                    <td>{{str_repeat('★', $review->rating)}}{{str_repeat('☆', 5-$review->rating)}}</td>
                    <td>{{$review->review}}</td>
                    <td>{{$review->reviewed_at->format('Y-m-d H:i')}}</td>
                  </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
@endsection


@section('scriptsAfterJs')
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip({trigger:'hover'});
        $('.sku-btn').click(function () {
            $('.product-info .price span').text($(this).data('price'));
            $('.product-info .stock').text('Stock: ' + $(this).data('stock'));
        });

        // on click 'Favor' button
        $('.btn-favor').click(function () {
          axios.post('{{route('products.favor', ['product' => $product->id])}}')
          .then(function () {
            swal('Favored', '', 'success')
            .then(function () {
              location.reload();
            })
          }, function (error) {
            if (error.response && error.response.status === 401) {
              swal('Please Login First', '', 'error');
            }else if (error.response && (error.response.data.msg || error.response.data.message)) {
              swal(error.response.data.msg ? error.response.data.msg : error.response.data.message, '', 'error');
            }else{
              swal('System Error', '', 'error');
            }
          });
        });

        // on click 'Disfavor' button
        $('.btn-disfavor').click(function () {
          axios.delete('{{ route('products.disfavor', ['product' => $product->id])}}')
          .then(function () {
            swal('Disfavored', '', 'success')
            .then(function () {
              location.reload();
            })
          })
        });

        // add to cart
        $('.btn-add-to-cart').click(function () {
          axios.post('{{route('cart.add')}}', {
          sku_id: $('label.active input[name=skus').val(),
          amount:$('.cart_amount input').val(),
        })
        .then(function () {
          swal('Added to Cart', '', 'success')
          .then(function () {
            location.href = '{{ route('cart.index')}}';
          });
        }, function (error) {
          if (error.response.status === 401) {
            swal('Please login first', '', 'error');
          }else if (error.response.status === 422) {
            var html = '<div>';
              _.each(error.response.data.errors, function (errors) {
                _.each(errors, function (error) {
                  html += error+'<br>';
                });
              });
            html += '</div>';
            swal({content: $(html)[0], icon: 'error'})
          }else {
            swal('System Error', '', 'error');
          };
        });
      });

    });
</script>
@endsection