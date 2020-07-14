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
                  <button class="btn btn-primary">Edit</button>
                  <button class="btn btn-danger">Delete</button>
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