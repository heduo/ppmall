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
    <div>
        <form class="form-horizontal" role="form" id="order-form">
            <div class="form-group row">
            <label class="col-form-label col-sm-3 text-md-right">Select an address</label>
            <div class="col-sm-9 col-md-7">
                <select class="form-control" name="address">
                @foreach($addresses as $address)
                    <option value="{{ $address->id }}">{{ $address->full_address }} -- {{ $address->contact_name }} -- {{ $address->contact_phone }}</option>
                @endforeach
                </select>
            </div>
            </div>
            <div class="form-group row">
            <label class="col-form-label col-sm-3 text-md-right">Remark</label>
            <div class="col-sm-9 col-md-7">
                <textarea name="remark" class="form-control" rows="3"></textarea>
            </div>
            </div>
            <div class="form-group">
            <div class="offset-sm-3 col-sm-3">
                <button type="button" class="btn btn-primary btn-create-order">Submit Order</button>
            </div>
            </div>
        </form>
    </div>

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

            // // on click create order
            $('.btn-create-order').click(function (){
              // request params
              var req = {
                address_id: $("#order-form").find('select[name=address]').val(),
                items:[],
                remark: $('#order-form').find('textarea[name=remark]').val()
              };

              $('table tr[data-id]').each(function (){
                // get current checkbox
                var $checkbox = $(this).find('input[name=select][type=checkbox]');
                
                // skip if checkbox is disabled or unchecked
                if ($checkbox.prop('disabled') || !$checkbox.prop('checked')) {
                  return;
                }

                var $input = $(this).find('input[name=amount]');

                // skip if amount is not valid
                if ($input.val() == 0 || isNaN($input.val())) {
                  return;
                }

                // set SKU id and amount
                req.items.push({
                  sku_id: $(this).data('id'),
                  amount: $input.val()
                });
              });

              axios.post('{{ route('orders.store') }}', req)
                .then(function (response) {
                  swal('Order Created', '', 'success')
                    .then(function () {
                      location.href = '/orders/' + response.data.id;
                    });
                }, function (error) {
                  if (error.response.status === 422) {
                    // http status code 422 means user input validation failure
                    var html = '<div>';
                    _.each(error.response.data.errors, function (errors) {
                      _.each(errors, function (error) {
                        html += error+'<br>';
                      })
                    });
                    html += '</div>';
                    swal({content: $(html)[0], icon: 'error'})
                  } else {
        
                    swal('System Error', '', 'error');
                  }
                });

            });  
        });

    </script>
@endsection
