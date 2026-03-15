<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        if (count($cart) === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('frontend.checkout.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string',
            'note' => 'nullable|string',
            'payment_method' => 'required|in:cod,qr',
        ]);

        $cart = session()->get('cart', []);

        if (count($cart) === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }

        $paymentMethod = $request->payment_method;
        $isQR = $paymentMethod === 'qr';

        $order = Order::create([
            'user_id' => Auth::id(),
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'note' => $request->note,
            'total_amount' => $total,
            'status' => $isQR ? 'awaiting_payment' : 'pending',
            'payment_method' => $paymentMethod,
            'payment_token' => $isQR ? Str::random(64) : null,
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['qty'],
                'price' => $item['price'],
            ]);
        }

        session()->forget('cart');

        if ($isQR) {
            return redirect()->route('checkout.qr', $order->id);
        }

        return redirect()->route('checkout.success', $order->id);
    }

    public function qrPayment(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->payment_method !== 'qr') {
            return redirect()->route('checkout.success', $order->id);
        }

        if ($order->status === 'completed') {
            return redirect()->route('checkout.success', $order->id);
        }

        $order->load('orderItems.product');

        return view('frontend.checkout.qr-payment', compact('order'));
    }

    public function qrConfirm($token)
    {
        $order = Order::where('payment_token', $token)
            ->where('status', 'awaiting_payment')
            ->firstOrFail();

        $order->update([
            'status' => 'completed',
        ]);

        return redirect()->route('checkout.success', $order->id);
    }

    public function qrStatus(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return response()->json([
            'paid' => $order->fresh()->status === 'completed',
        ]);
    }

    public function success(Order $order)
    {
        $order->load('orderItems.product');

        return view('frontend.checkout.success', compact('order'));
    }
}