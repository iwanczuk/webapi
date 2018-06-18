<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\{Product,Cart,Item};

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/products', function (Request $request) use ($router) {
    return new \App\Http\Resources\ProductCollection(Product::paginate(3));
});

$router->post('/products', function (Request $request) use ($router) {
    $product = new Product();
    $product->name = $request->request->get('name');
    $product->price = $request->request->get('price');
    $product->save();

    return new \App\Http\Resources\Product($product);
});

$router->patch('/products/{id}', function (Request $request, $id) use ($router) {
    try {
        $product = Product::findOrFail($id);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Product not found.'], 404);
    }

    $product->name = $request->request->get('name', $product->name);
    $product->price = $request->request->get('price', $product->price);
    $product->save();

    return new \App\Http\Resources\Product($product);
});

$router->delete('/products/{id}', function ($id) use ($router) {
    try {
        $product = Product::findOrFail($id);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Product not found.'], 404);
    }

    $product->forceDelete();

    return response()->json([], 204);
});

$router->get('/cart/{id}', function ($id) use ($router) {
    try {
        $cart = Cart::findOrFail($id);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Cart not found.'], 404);
    }

    return new \App\Http\Resources\Cart($cart);
});

$router->post('/cart', function () use ($router) {
    $cart = new Cart();
    $cart->save();

    return new \App\Http\Resources\Cart($cart);
});

$router->post('/cart/{id}/item', function (Request $request, $id) use ($router) {
    try {
        $product = Product::findOrFail($request->request->get('product_id'));
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Product not found.'], 404);
    }

    try {
        $cart = Cart::findOrFail($id);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Cart not found.'], 404);
    }

    if ($cart->items->contains('product_id', $product->id)) {
        return response()->json(['error' => 'Cart contains product.'], 400);
    }

    if ($cart->items->count() >= 3) {
        return response()->json(['error' => 'Cart is full.'], 400);
    }

    $item = new Item();
    $item->cart_id = $cart->id;
    $item->product_id = $product->id;
    $item->save();

    return new \App\Http\Resources\Item($item);
});

$router->delete('/cart/{id}/item/{item_id}', function ($id, $itemId) use ($router) {
    try {
        $cart = Cart::findOrFail($id);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Cart not found.'], 404);
    }

    if (!$cart->items->contains('id', $itemId)) {
        return response()->json(['error' => 'Cart does not contain item.'], 400);
    }

    $item = $cart->items->where('id', $itemId)->first();
    $item->forceDelete();

    return response()->json([], 204);
});
