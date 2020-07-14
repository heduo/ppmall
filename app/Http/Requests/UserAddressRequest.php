<?php

namespace App\Http\Requests;

class UserAddressRequest extends Request
{
    public function rules()
    {
        return [
            'country'      => 'required',
            'state'          => 'required',
            'suburb'      => 'required',
            'address1'       => 'required',
            //'address2'     => 'required',
            'contact_name'  => 'required',
            'contact_phone' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'country'      => 'Country',
            'state'          => 'State',
            'suburb'      => 'Suburb',
            'address1'       => 'Address',
            'address2'     => 'Address2',
            'contact_name'  => 'Contact Name',
            'contact_phone' => 'Contact Phone',
        ];
    }
}