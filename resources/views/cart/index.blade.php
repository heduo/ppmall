@extends('layouts.app')
@section('title', 'Shopping Cart')

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-header">My Cart</div>
  <div class="card-body">
    <table class="table table-striped">
      <thead>
      <tr>
        <th><input type="checkbox" id="select-all"></th>
        <th>Item</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Action</th>
      </tr>
      </thead>
      <tbody class="product_list">
      @foreach($cartItems as $item)
        <tr data-id="{{ $item->productSku->id }}">
          <td>
            <input type="checkbox" name="select" value="{{ $item->productSku->id }}" {{ $item->productSku->product->on_sale ? 'checked' : 'disabled' }}>
          </td>
          <td class="product_info">
            <div class="preview">
              <a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">
                <img src="{{ $item->productSku->product->image_url }}">
              </a>
            </div>
            <div @if(!$item->productSku->product->on_sale) class="not_on_sale" @endif>
              <span class="product_title">
                <a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">{{ $item->productSku->product->title }}</a>
              </span>
              <span class="sku_title">{{ $item->productSku->title }}</span>
              @if(!$item->productSku->product->on_sale)
                <span class="warning">This product is not on sale</span>
              @endif
            </div>
          </td>
          <td><span class="price">A${{ $item->productSku->price }}</span></td>
          <td>
            <input type="text" class="form-control form-control-sm amount" @if(!$item->productSku->product->on_sale) disabled @endif name="amount" value="{{ $item->amount }}">
          </td>
          <td>
            <button class="btn btn-sm btn-danger btn-remove">Remove</button>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
</div>
</div>
@endsection

@section('scriptsAfterJs')
    <script>
        $(document).ready(function () {
            // on click remove button
            $('.btn-remove').click(function () {
            // $(this) 可以获取到当前点击的 移除 按钮的 jQuery 对象
            // data('id') get value of attr 'data-id', whichi is  SKU id
            var id = $(this).closest('tr').data('id');
            swal({
                title: "Are you sure to remove this item？",
                icon: "warning",
                buttons: ['Cancel', 'Confirm'],
                dangerMode: true,
            })
            .then(function(willDelete) {
               
                if (!willDelete) {
                return;
                }
                axios.delete('/cart/' + id)
                .then(function () {
                    location.reload();
                })
            });
            });

            // 监听 全选/取消全选 单选框的变更事件
            $('#select-all').change(function() {
                var checked = $(this).prop('checked');
        
                // select all the undisabled checkbox 
                $('input[name=select][type=checkbox]:not([disabled])').each(function() {
                    // 将其勾选状态设为与目标单选框一致
                    $(this).prop('checked', checked);
                });
            });    
        });

    </script>
@endsection