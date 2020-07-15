<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        // query builder
        $builder = Product::query()->where('on_sale', true);

        // if search input is not empty, search products
        if ($search=$request->input('search', '')) {
            $like = '%'.$search.'%';
            // search product
            $builder->where(function ($query) use ($like)
            {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like)
                    {
                       $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });

        }

        // sort products
        if ($order = $request->input('order', '')) {
            // ends with '_asc' or '_desc'
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $matches)) {

                if (in_array($matches[1], ['price', 'sold_count', 'rating'])) {
                    $builder->orderBy($matches[1], $matches[2]);
                }
            }
        }

        $products = $builder->paginate(16);

        return view('products.index', [
            'products'=>$products,
            'filters' => [
                'search' => $search,
                'order' => $order
            ]
            ]);
    }
}
