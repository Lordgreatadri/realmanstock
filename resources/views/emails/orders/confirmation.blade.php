<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .order-details {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            border: 1px solid #e5e7eb;
        }
        .order-number {
            font-size: 24px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 10px;
        }
        .detail-row {
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #6b7280;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th {
            background-color: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #e5e7eb;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .total-row {
            font-weight: bold;
            font-size: 18px;
            color: #4F46E5;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #4F46E5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-processing {
            background-color: #e0e7ff;
            color: #3730a3;
        }
        .status-payment_received {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .status-ready_for_delivery {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-out_for_delivery {
            background-color: #e0e7ff;
            color: #5b21b6;
        }
        .status-delivered {
            background-color: #d1fae5;
            color: #047857;
        }
        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RealMan Livestock</h1>
        <p>Order Confirmation</p>
    </div>

    <div class="content">
        <h2>Hello {{ $customer->name }}!</h2>
        <p>Thank you for your order. We've received it and it's being processed.</p>

        <div class="order-details">
            <div class="order-number">Order #{{ $order->order_number }}</div>
            
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                {{ $order->created_at->format('F d, Y h:i A') }}
            </div>

            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="status-badge status-{{ $order->status }}">
                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </span>
            </div>

            @if($order->delivery_date)
            <div class="detail-row">
                <span class="detail-label">Expected Delivery:</span>
                {{ \Carbon\Carbon::parse($order->delivery_date)->format('F d, Y') }}
            </div>
            @endif

            @if($order->notes)
            <div class="detail-row">
                <span class="detail-label">Notes:</span>
                {{ $order->notes }}
            </div>
            @endif
        </div>

        <h3>Order Items</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->quantity }} {{ $item->unit }}</td>
                    <td>GH₵ {{ number_format($item->unit_price, 2) }}</td>
                    <td>GH₵ {{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">Total:</td>
                    <td>GH₵ {{ number_format($order->total, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div style="text-align: center;">
            <a href="{{ route('manager.orders.show', $order) }}" class="button">View Order Details</a>
        </div>

        <p><strong>What's Next?</strong></p>
        <ul>
            <li>We'll send you notifications via SMS and email when your order status changes</li>
            <li>You can track your order status using your order number</li>
            @if($order->status === 'pending')
                <li>Your order is being reviewed and will be processed shortly</li>
            @elseif($order->status === 'processing')
                <li>Your order is currently being prepared</li>
            @elseif($order->status === 'payment_received')
                <li>Payment confirmed! Your order is now in the queue for processing</li>
            @elseif($order->status === 'ready_for_delivery')
                <li>Your order is ready for pickup or delivery</li>
            @elseif($order->status === 'out_for_delivery')
                <li>Your order is on the way to you</li>
            @endif
        </ul>

        <p>If you have any questions, please contact us.</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} RealMan Livestock. All rights reserved.</p>
        <p>This is an automated email. Please do not reply.</p>
    </div>
</body>
</html>
