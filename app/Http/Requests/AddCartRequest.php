<?php

namespace App\Http\Requests;

use App\Models\ProductSku;

class AddCartRequest extends Request
{
    public function rules()
    {
        return [
            'sku_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$sku = ProductSku::find($value)) {
                        return $fail('This item does not exist');
                    }
                    if (!$sku->product->on_sale) {
                        return $fail('This item is not on sale');
                    }
                    if ($sku->stock === 0) {
                        return $fail('This item is sold out');
                    }
                    if ($this->input('amount') > 0 && $sku->stock < $this->input('amount')) {
                        return $fail('The stock is not enough');
                    }
                },
            ],
            'amount' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes()
    {
        return [
            'amount' => 'Quantity '
        ];
    }

    public function messages()
    {
        return [
            'sku_id.required' => 'Please add a item'
        ];
    }
}