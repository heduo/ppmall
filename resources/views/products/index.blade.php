@extends('layouts.app')
@section('title', 'Products List')

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-body">
    <div class="row products-list">
      @foreach($products as $product)
        <div class="col-3 product-item">
          <div class="product-content">
            <div class="top">
              <div class="img"><img src="{{ $product->image_url }}" alt=""></div>
              <div class="price"><b>$A</b>{{ $product->price }}</div>
              <div class="title">{{ $product->title }}</div>
            </div>
            <div class="bottom">
              <div class="sold_count">Sold <span>{{ $product->sold_count }}</span></div>
              <div class="review_count">Reviews <span>{{ $product->review_count }}</span></div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
<div class="float-right">{{ $products->render() }}</div>
  </div>
</div>
</div>
</div>
@endsection