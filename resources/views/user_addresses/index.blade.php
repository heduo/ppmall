@extends('layouts.app')
@section('title', 'Shipping Addresses')

@section('content')
  <div class="row">
    <div class="col-md-10 offset-md-1">
      <div class="card panel-default">
        <div class="card-header">Shipping Addresses List
          <a href="{{route('user_addresses.create')}}" class="float-right">Add New Address</a>
        </div>
        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>Receiver</th>
              <th>Address</th>
              <th>Post</th>
              <th>Telephone</th>
              <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($addresses as $address)
              <tr>
                <td>{{ $address->contact_name }}</td>
                <td>{{ $address->full_address }}</td>
                <td>{{ $address->postcode }}</td>
                <td>{{ $address->contact_phone }}</td>
                <td>
                <a href="{{route('user_addresses.edit', ['user_address'=>$address->id])}}" class="btn btn-primary">Edit</a>
                  
                <button class="btn btn-danger btn-del-address" data-id="{{$address->id}}">Delete</button>
                  
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div>

      </div>
    </div>
  </div>
@endsection

@section('scriptsAfterJs')
  <script>
    $(document).ready(function() {
      // when click delete button
      $('.btn-del-address').click(function () {
        // get address id from data-id
        var id = $(this).data('id');
        // invoke sweetalert
        swal({
          title: "Do you want to delete this address ?",
          icon: "warning",
          buttons: ['Cancel', 'Confirm'],
          dangerMode: true
        }).then(function (willDelete) {
          if (!willDelete) {
            return;
          }
          // send request for deletion
          axios.delete('/user_addresses/'+id)
          .then(function () {
            location.reload();
          })
        })
      })
    })
  </script>
@endsection
