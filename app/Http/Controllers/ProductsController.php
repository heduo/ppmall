<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
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

    public function show(Product $product, Request $request)
    {
        // if product is ot on sale
        if (!$product->on_sale) {
            throw new InvalidRequestException('Product not on sale');
        }

        $favored = false;

        if ($user = $request->user()) {
            $favored = boolval($user->favoriteProducts()->find($product->id)); // check if this product is favored
        }

        return view('products.show', ['product' => $product, 'favored' => $favored]);
    }

    public function favor(Product $product, Request $request)
    {
        $user = $request->user();

        // if the product is favored, do nothing
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        // otherwise, attach user to this product
        $user->favoriteProducts()->attach($product);

        return [];
    }

    public function disfavor(Product $product, Request $request)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product);

        return [];
    }

    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate(16);

        return view('products.favorites', ['products' => $products]);
    }
}
