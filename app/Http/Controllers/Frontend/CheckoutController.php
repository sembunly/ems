<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

        DB::beginTransaction();

        try {
            $total = 0;
            $products = [];

            // Check product and stock first
            foreach ($cart as $item) {
                $product = Product::where('id', $item['id'])->lockForUpdate()->first();

                if (! $product) {
                    DB::rollBack();
                    return redirect()->route('cart.index')->with('error', 'Product not found.');
                }

                if ($product->stock < $item['qty']) {
                    DB::rollBack();
                    return redirect()->route('cart.index')->with('error', $product->name . ' stock not enough.');
                }

                $products[$product->id] = $product;
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

                // Cut stock only for COD
                if (! $isQR) {
                    $products[$item['id']]->decrement('stock', $item['qty']);
                }
            }

            DB::commit();

            // Send Telegram only for COD
            if (! $isQR) {
                $this->sendTelegramMessage($order, $cart, 'New COD Order');
            }

            session()->forget('cart');

            if ($isQR) {
                return redirect()->route('checkout.qr', $order->id);
            }

            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'Order placed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage());
        }
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
        DB::beginTransaction();

        try {
            $order = Order::where('payment_token', $token)
                ->where('status', 'awaiting_payment')
                ->firstOrFail();

            $order->load('orderItems.product');

            // Cut stock after QR payment confirmed
            foreach ($order->orderItems as $orderItem) {
                $product = Product::where('id', $orderItem->product_id)->lockForUpdate()->first();

                if (! $product) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Product not found.');
                }
                if ($product->stock < $orderItem->quantity) {
                    DB::rollBack();
                    return redirect()->back()->with('error', $product->name . ' stock not enough.');
                }

                $product->decrement('stock', $orderItem->quantity);
            }

            $order->update([
                'status' => 'completed',
            ]);

            DB::commit();

            $items = [];
            foreach ($order->orderItems as $orderItem) {
                $items[] = [
                    'name' => optional($orderItem->product)->name ?? 'Product',
                    'qty' => $orderItem->quantity,
                    'price' => $orderItem->price,
                ];
            }

            $this->sendTelegramMessage($order, $items, 'QR Payment Confirmed');

            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'QR payment confirmed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage());
        }
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
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('orderItems.product');

        return view('frontend.checkout.success', compact('order'));
    }

    private function sendTelegramMessage($order, $items, $title = 'New Order')
    {
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (! $botToken || ! $chatId) {
            Log::warning('Telegram config missing.');
            return;
        }

        $message = "{$title}\n\n";
        $message .= "Order ID: #{$order->id}\n";
        $message .= "Customer: {$order->full_name}\n";
        $message .= "Phone: {$order->phone}\n";
        $message .= "Address: {$order->address}\n";
        $message .= "Payment: " . strtoupper($order->payment_method) . "\n";
        $message .= "Status: {$order->status}\n";
        $message .= "Note: " . ($order->note ?: 'N/A') . "\n\n";
        $message .= "Items:\n";

        foreach ($items as $item) {
            $name = $item['name'] ?? 'Product';
            $qty = $item['qty'] ?? 0;
            $price = $item['price'] ?? 0;

            $message .= "- {$name} | Qty: {$qty} | Price: $" . number_format($price, 2) . "\n";
        }

        $message .= "\nTotal: $" . number_format($order->total_amount, 2);

        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
            ]);

            if (! $response->successful()) {
                Log::error('Telegram API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Telegram send failed: ' . $e->getMessage());
        }
    }
}