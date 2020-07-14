@extends('layouts.app')
@section('title', 'Add Shipping Address')

@section('content')
<div>
    <div class="card">
        <div class="card-header">
            <h3 class="text-center">
                Add New Shipping Address
            </h3>
        </div>
        <div class="card-body">
            @if(count($errors)>0)
                <div class="alert alert-danger">
                    <h4>Erors:</h4>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li><i class="glyphicon glyphicon-remove"></i>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
           
            <address-form inline-template>
                <form class="form-horizontal" role="form" action="{{route('user_addresses.store')}}" method="post">
                    {{ csrf_field()}}
                    <div>
                        <div class="form-group jumbotron">
                          <h5 for="autocomplete">Address Helper</h5>
                          <input
                            type="text"
                            class="form-control"
                            id="autocomplete"
                            name="autocomplete"
                            placeholder="Enter your address"
                          />
                        </div>
                        <div class="form-group"></div>
                    
                        <div class="form-row">
                          <div class="form-group col-md-6">
                            <label for="contact_name" class="font-weight-bolder">Contact Name *</label>
                            <input type="text" class="form-control" id="contact_name" name="contact_name"  value="{{old('contact_name',$address->contact_name)}}"/>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="contact_phone" class="font-weight-bolder">Contact Phone *</label>
                            <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="{{old('contact_phone',$address->contact_phone)}}"/>
                          </div>
                        </div>
                        <div>
                            <!-- hidden street number and street name for address1 calc -->
                            <input type="text" hidden class="form-control" id="street_number" name="street_number" v-model="street_number" v-on:change="updateAddress1" />
                            <input type="text" hidden class="form-control" id="route" name="street_name" v-model="street_name" v-on:change="updateAddress1"/>
                        </div>
                        <div class="form-group">
                            <label for="address1" class="font-weight-bolder">Address *</label>
                            <input
                              type="text"
                              class="form-control"
                              id="address1"
                              name="address1"
                              placeholder="St, Rd, Ave , etc"
                              value="{{old('address1',$address->address1)}}"
                            />
                        </div>
                        <div class="form-group">
                          <label for="address2" class="font-weight-bolder">Address 2</label>
                          <input
                            type="text"
                            class="form-control"
                            id="address2"
                            name="address2"
                            placeholder="Apartment, studio, or floor"
                            value="{{old('address2',$address->address2)}}"
                          />
                        </div>
                        <div class="form-row">
                          <div class="form-group col-md-5">
                            <label for="inputCity" class="font-weight-bolder">Suburb *</label>
                            <input type="text" class="form-control" id="locality" name="suburb" value="{{old('suburb',$address->suburb)}}"/>
                          </div>
                          <div class="form-group col-md-5">
                            <label for="inputState" class="font-weight-bolder">State *</label>
                            <input type="text" class="form-control" id="administrative_area_level_1" name="state" value="{{old('state',$address->state)}}"/>
                          </div>
                          <div class="form-group col-md-2">
                            <label for="inputZip" class="font-weight-bolder">Postcode *</label>
                            <input type="text" class="form-control" id="postal_code" name="postcode" value="{{old('postcode',$address->postcode)}}"/>
                          </div>
                        </div>
                        <div class="form-group">
                          <label for="autocomplete" class="font-weight-bolder">Country *</label>
                          <input type="text" class="form-control" id="country" name="country"  value="{{old('country',$address->country)}}" />
                        </div>
                      </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" v-on:click="updateAddress1">Submit</button>
                    </div>
                </form>
            </address-form>
               
        </div>
    </div>
   
    
</div>
@endsection