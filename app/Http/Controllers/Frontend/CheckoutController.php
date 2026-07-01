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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

// Mailable classes
use App\Mail\OrderInvoiceMail;

// Bakong KHQR package
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;

class CheckoutController extends Controller
{
    /**
     * Show checkout page
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        // If cart is empty, go back to cart page
        if (count($cart) === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('frontend.checkout.index', compact('cart'));
    }

    /**
     * Store order after customer submits checkout form
     */
    public function store(Request $request)
    {
        // Validate checkout form input
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string',
            'note' => 'nullable|string',
            'payment_method' => 'required|in:cod,qr',
        ]);

        $cart = session()->get('cart', []);

        // Prevent checkout if cart is empty
        if (count($cart) === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        DB::beginTransaction();

        try {
            $total = 0;
            $products = [];
            $checkoutItems = [];

            // Check product existence and stock before creating order
            foreach ($cart as $item) {
                $product = Product::where('id', $item['id'])->lockForUpdate()->first();

                if (!$product) {
                    DB::rollBack();
                    return redirect()->route('cart.index')->with('error', 'Product not found.');
                }

                if ($product->stock < $item['qty']) {
                    DB::rollBack();
                    return redirect()->route('cart.index')->with('error', $product->name . ' stock not enough.');
                }

                $products[$product->id] = $product;
                $price = $product->price;
                $qty = (int) $item['qty'];

                $checkoutItems[$product->id] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $price,
                    'qty' => $qty,
                ];

                $total += $price * $qty;
            }

            $paymentMethod = $request->payment_method;
            $isQR = $paymentMethod === 'qr';

            // Create order
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

            // Create order items
            foreach ($checkoutItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                ]);

                // Reduce stock immediately only for COD
                if (!$isQR) {
                    $products[$item['id']]->decrement('stock', $item['qty']);
                }
            }

            DB::commit();

            // Load order items for email
            $order->load('orderItems.product');

            // Send Invoice Email to customer
            try {
                $user = Auth::user();
                if ($user && $user->email) {
                    Log::info("Attempting to send invoice email to: {$user->email} for order #{$order->id}");

                    Mail::to($user->email)->send(new OrderInvoiceMail($order));

                    Log::info("SUCCESS: Invoice email sent to {$user->email} for order #{$order->id}");
                } else {
                    Log::warning("Cannot send invoice email: User email not available for order #{$order->id}");
                }
            } catch (\Exception $e) {
                Log::error("FAILED to send invoice email to {$user->email} for order #{$order->id}. Error: " . $e->getMessage());
                Log::error("Exception details: " . $e->getTraceAsString());
            }

            // Send Telegram notification only for COD
            if (!$isQR) {
                $telegramItems = [];

                foreach ($checkoutItems as $item) {
                    $telegramItems[] = [
                        'name' => $item['name'] ?? 'Product',
                        'qty' => $item['qty'],
                        'price' => $item['price'],
                    ];
                }

                $this->sendTelegramMessage($order, $telegramItems, 'New COD Order');
            }

            // Clear cart after order created
            session()->forget('cart');

            // If QR payment selected, go to QR payment page
            if ($isQR) {
                return redirect()->route('checkout.qr', $order->id);
            }

            // If COD, go directly to success page
            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'Order placed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout store error: ' . $e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    //  * Show QR payment page and generate Bakong KHQR
    public function qrPayment(Order $order)
    {
        // Prevent other users from accessing this order
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // If this order is not QR payment, redirect to success page
        if ($order->payment_method !== 'qr') {
            return redirect()->route('checkout.success', $order->id);
        }

        // If already completed, redirect to success page
        if ($order->status === 'completed') {
            return redirect()->route('checkout.success', $order->id);
        }

        $order->load('orderItems.product');

        try {
            // Create merchant info for Bakong KHQR
            $merchant = new IndividualInfo(
                bakongAccountID: env('BAKONG_ACCOUNT_ID'),
                merchantName: env('BAKONG_MERCHANT_NAME'),
                merchantCity: env('BAKONG_MERCHANT_CITY'),
                currency: KHQRData::CURRENCY_USD,
                amount: (float) $order->total_amount
            );

            // Generate KHQR
            $qrResponse = BakongKHQR::generateIndividual($merchant);

            // Get QR raw string and md5 transaction reference
            $qr = $qrResponse->data['qr'] ?? null;
            $md5 = $qrResponse->data['md5'] ?? null;

            return view('frontend.checkout.qr-payment', compact('order', 'qr', 'md5'));
        } catch (\Exception $e) {
            Log::error('QR generation error: ' . $e->getMessage());

            return redirect()->route('checkout.index')
                ->with('error', 'Unable to generate KHQR.');
        }
    }

    /**
     * Optional verify form page
     */
    public function verifyForm()
    {
        return view('payments.verify');
    }

    /**
     * Verify Bakong transaction using md5
     */
    public function verifyTransaction(Request $request)
    {
        // Validate request
        $request->validate([
            'md5' => 'required|string',
            'order_id' => 'required|exists:orders,id',
        ]);

        try {
            $order = Order::findOrFail($request->order_id);

            // Make sure user owns the order
            if ($order->user_id !== Auth::id()) {
                abort(403);
            }

            // Make sure this is QR payment
            if ($order->payment_method !== 'qr') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid payment method.',
                ], 400);
            }

            // If already completed, return paid=true
            if ($order->status === 'completed') {
                return response()->json([
                    'success' => true,
                    'paid' => true,
                    'message' => 'Order already paid.',
                    'redirect' => route('checkout.success', $order->id),
                ]);
            }

            $token = env('BAKONG_TOKEN');

            // Bakong token required
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bakong token not configured.',
                ], 500);
            }

            // Ask Bakong API to verify this md5 transaction
            $bakong = new BakongKHQR($token);
            $result = $bakong->checkTransactionByMD5($request->md5);

            $paid = false;

            // Check response format from Bakong
            if (
                (isset($result['responseCode']) && $result['responseCode'] == 0) ||
                (isset($result['status']) && in_array(strtolower($result['status']), ['success', 'paid', 'completed'])) ||
                (isset($result['data']['status']) && in_array(strtolower($result['data']['status']), ['success', 'paid', 'completed']))
            ) {
                $paid = true;
            }

            // If paid, update order and cut stock
            if ($paid) {
                DB::beginTransaction();

                $order->load('orderItems.product');

                foreach ($order->orderItems as $orderItem) {
                    $product = Product::where('id', $orderItem->product_id)->lockForUpdate()->first();

                    if (!$product) {
                        DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' => 'Product not found.',
                        ], 404);
                    }

                    if ($product->stock < $orderItem->quantity) {
                        DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' => $product->name . ' stock not enough.',
                        ], 400);
                    }

                    $product->decrement('stock', $orderItem->quantity);
                }

                // Mark order as completed
                $order->update([
                    'status' => 'completed',
                ]);

                DB::commit();

                // Prepare Telegram items
                $items = [];
                foreach ($order->orderItems as $orderItem) {
                    $items[] = [
                        'name' => optional($orderItem->product)->name ?? 'Product',
                        'qty' => $orderItem->quantity,
                        'price' => $orderItem->price,
                    ];
                }

                // Send Telegram notification
                $this->sendTelegramMessage($order, $items, 'QR Payment Confirmed');

                // Send Invoice Email to customer
                try {
                    $user = \App\Models\User::find($order->user_id);
                    if ($user && $user->email) {
                        Log::info("Attempting to send invoice email to: {$user->email} for order #{$order->id} (QR payment verified)");

                        Mail::to($user->email)->send(new OrderInvoiceMail($order));

                        Log::info("SUCCESS: Invoice email sent to {$user->email} for order #{$order->id} (QR payment verified)");
                    } else {
                        Log::warning("Cannot send invoice email: User email not available for order #{$order->id}");
                    }
                } catch (\Exception $e) {
                    Log::error("FAILED to send invoice email to {$user->email} for order #{$order->id}. Error: " . $e->getMessage());
                    Log::error("Exception details: " . $e->getTraceAsString());
                }

                return response()->json([
                    'success' => true,
                    'paid' => true,
                    'message' => 'QR payment confirmed successfully.',
                    'redirect' => route('checkout.success', $order->id),
                    'bakong_response' => $result,
                ]);
            }

            // If not paid yet
            return response()->json([
                'success' => true,
                'paid' => false,
                'message' => 'Payment not completed yet.',
                'bakong_response' => $result,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bakong verify error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    //Manual QR confirm using payment token
    //This is old flow, optional if you still want to keep it
    public function qrConfirm($token)
    {
        DB::beginTransaction();

        try {
            $order = Order::where('payment_token', $token)
                ->where('status', 'awaiting_payment')
                ->firstOrFail();

            $order->load('orderItems.product');

            // Reduce stock after confirming payment
            foreach ($order->orderItems as $orderItem) {
                $product = Product::where('id', $orderItem->product_id)->lockForUpdate()->first();

                if (!$product) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Product not found.');
                }

                if ($product->stock < $orderItem->quantity) {
                    DB::rollBack();
                    return redirect()->back()->with('error', $product->name . ' stock not enough.');
                }

                $product->decrement('stock', $orderItem->quantity);
            }

            // Update order status
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

            // Send Invoice Email to customer
            try {
                $user = \App\Models\User::find($order->user_id);
                if ($user && $user->email) {
                    Log::info("Attempting to send invoice email to: {$user->email} for order #{$order->id} (QR payment confirmed)");

                    Mail::to($user->email)->send(new OrderInvoiceMail($order));

                    Log::info("SUCCESS: Invoice email sent to {$user->email} for order #{$order->id} (QR payment confirmed)");
                } else {
                    Log::warning("Cannot send invoice email: User email not available for order #{$order->id}");
                }
            } catch (\Exception $e) {
                Log::error("FAILED to send invoice email to {$user->email} for order #{$order->id}. Error: " . $e->getMessage());
                Log::error("Exception details: " . $e->getTraceAsString());
            }

            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'QR payment confirmed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('QR confirm error: ' . $e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Check local order payment status from database
     */
    public function qrStatus(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return response()->json([
            'paid' => $order->fresh()->status === 'completed',
        ]);
    }

    // Optional payment result page
    public function paymentResult()
    {
        return view('payments.result');
    }

    //Show success page after order/payment completed
    public function success(Order $order)
    {
        // Prevent other users from viewing this order
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('orderItems.product');

        return view('frontend.checkout.success', compact('order'));
    }

    // Send order message to Telegram
    private function sendTelegramMessage($order, $items, $title = 'New Order')
    {
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        // Stop if Telegram config missing
        if (!$botToken || !$chatId) {
            Log::warning('Telegram config missing.');
            return;
        }

        // Build message
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

            if (!$response->successful()) {
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
