<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\ProductSku;

class OrderRequest extends Request
{
    public function rules()
    {
        return [

            'address_id'     => [
                'required',
                Rule::exists('user_addresses', 'id')->where('user_id', $this->user()->id),
            ],
            'items'  => ['required', 'array'],
            'items.*.sku_id' => [ // check if each item's sku_id
                'required',
                function ($attribute, $value, $fail) {
                    if (!$sku = ProductSku::find($value)) {
                        return $fail('This item does not exist');
                    }
                    if (!$sku->product->on_sale) {
                        return $fail('This item is not on sale');
                    }
                    if ($sku->stock === 0) {
                        return $fail('No stock of this item');
                    }
                    // get index of current item
                    preg_match('/items\.(\d+)\.sku_id/', $attribute, $matches);
                    $index = $matches[1];
                    // get amount
                    $amount = $this->input('items')[$index]['amount'];
                    if ($amount > 0 && $amount > $sku->stock) {
                        return $fail('Stock is not enought');
                    }
                },
            ],
            'items.*.amount' => ['required', 'integer', 'min:1'],
        ];
    }
}