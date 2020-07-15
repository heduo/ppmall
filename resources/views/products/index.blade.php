@extends('layouts.app')
@section('title', 'Products List')

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-body">
    <!-- Filter Start -->
    <form action="{{ route('products.index') }}" class="search-form">
      <div class="form-row">
        <div class="col-md-9">
          <div class="form-row">
            <div class="col-auto"><input type="text" class="form-control form-control-sm" name="search" placeholder="Product Name"></div>
            <div class="col-auto"><button class="btn btn-primary btn-sm">Search</button></div>
          </div>
        </div>
        <div class="col-md-3">
          <select name="order" class="form-control form-control-sm float-right">
            <option value="">Sort</option>
            <option value="price_asc">Price ASC</option>
            <option value="price_desc">Price DESC</option>
            <option value="sold_count_desc">Sales DESC</option>
            <option value="sold_count_asc">Sales ASC</option>
            <option value="rating_desc">Rating DESC</option>
            <option value="rating_asc">Rating ASC</option>
          </select>
        </div>
      </div>
    </form>
     <!-- Filter End -->
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
    <div class="float-right">{{ $products->appends($filters)->render() }}</div>
  </div>
</div>
</div>
</div>
@endsection

@section('scriptsAfterJs')
  <script>
    var filters = {!! json_encode($filters) !!};
    console.log(filters);
    $(document).ready(function () {
      $('.search-form input[name=search]').val(filters.search);
      $('.search-form select[name=order]').val(filters.order);

      $('.search-form select[name=order]').on('change', function () {
        $('.search-form').submit();
      })
    })
  </script>
@endsection