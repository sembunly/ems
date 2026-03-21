<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('frontend.cart.index', compact('cart'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $qty = (int) $request->input('qty', 1);
        $qty = $qty > 0 ? $qty : 1;

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['qty'] += $qty;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'qty' => $qty,
            ];
        }

        session()->put('cart', $cart);

        $cartCount = collect($cart)->sum('qty');

        // Check if AJAX request
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => $product->name . ' added to cart!',
                'cart_count' => $cartCount,
            ]);
        }

        return back()->with('success', 'Product added to cart.');
    }

    public function update(Request $request, $id)
    {
        $qty = (int) $request->input('qty', 1);
        $qty = $qty > 0 ? $qty : 1;

        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return response()->json([
                'ok' => false,
                'message' => 'Product not found in cart.'
            ], 404);
        }

        $cart[$id]['qty'] = $qty;
        session()->put('cart', $cart);

        $lineTotal = ((float)$cart[$id]['price']) * ((int)$cart[$id]['qty']);

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ((float)$item['price']) * ((int)$item['qty']);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Quantity updated successfully.',
            'line_total' => number_format($lineTotal, 2),
            'subtotal' => number_format($subtotal, 2),
            'cart_count' => collect($cart)->sum('qty'),
        ]);
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return response()->json([
                'ok' => false,
                'message' => 'Product not found in cart.'
            ], 404);
        }

        unset($cart[$id]);
        session()->put('cart', $cart);

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ((float)$item['price']) * ((int)$item['qty']);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Product removed from cart.',
            'subtotal' => number_format($subtotal, 2),
            'cart_count' => collect($cart)->sum('qty'),
        ]);
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Cart cleared successfully.');
    }
}