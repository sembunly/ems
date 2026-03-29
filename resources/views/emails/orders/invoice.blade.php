<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice for Order #{{ $order->id }}</title>
    <style>
        /* Inline styles for email compatibility */
        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #0d6efd;
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .header p {
            color: #666;
            margin: 0;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .row {
            margin-bottom: 10px;
            overflow: hidden;
        }
        .label {
            float: left;
            width: 40%;
            font-weight: bold;
            color: #666;
        }
        .value {
            float: left;
            width: 60%;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #0d6efd;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #e7f3ff !important;
            font-weight: bold;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }
        .badge-danger {
            background-color: #dc3545;
        }
        .note {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 15px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>GenZ Laptop Store</h1>
            <p>Thank you for your order!</p>
        </div>

        <div class="section">
            <div class="section-title">Order Information</div>
            <div class="row">
                <div class="label">Order ID:</div>
                <div class="value">#{{ $order->id }}</div>
                <div class="clear"></div>
            </div>
            <div class="row">
                <div class="label">Order Date:</div>
                <div class="value">{{ $order->created_at->format('F d, Y - h:i A') }}</div>
                <div class="clear"></div>
            </div>
            <div class="row">
                <div class="label">Payment Method:</div>
                <div class="value">{{ strtoupper($order->payment_method) }}</div>
                <div class="clear"></div>
            </div>
            <div class="row">
                <div class="label">Status:</div>
                <div class="value">
                    @if($order->status === 'completed')
                        <span class="badge badge-success">Completed</span>
                    @elseif($order->status === 'pending')
                        <span class="badge badge-warning">Pending</span>
                    @elseif($order->status === 'awaiting_payment')
                        <span class="badge badge-danger">Awaiting Payment</span>
                    @else
                        {{ $order->status }}
                    @endif
                </div>
                <div class="clear"></div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Items Ordered</div>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'Product' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">${{ number_format($item->price, 2) }}</td>
                        <td class="text-right">${{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3" class="text-right">Total Amount:</td>
                        <td class="text-right">${{ number_format($total, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Shipping Information</div>
            <div class="row">
                <div class="label">Customer Name:</div>
                <div class="value">{{ $order->full_name }}</div>
                <div class="clear"></div>
            </div>
            <div class="row">
                <div class="label">Phone:</div>
                <div class="value">{{ $order->phone }}</div>
                <div class="clear"></div>
            </div>
            <div class="row">
                <div class="label">Shipping Address:</div>
                <div class="value">{{ $order->address }}</div>
                <div class="clear"></div>
            </div>
            @if($order->note)
            <div class="row">
                <div class="label">Note:</div>
                <div class="value">{{ $order->note }}</div>
                <div class="clear"></div>
            </div>
            @endif
        </div>

        @if($order->status === 'awaiting_payment')
        <div class="note">
            <strong>Note:</strong> Your order is awaiting payment. Please complete your payment to process the order.
        </div>
        @endif

        <div class="footer">
            <p>If you have any questions about your order, please contact our support team.</p>
            <p>Thank you for shopping with us!</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
