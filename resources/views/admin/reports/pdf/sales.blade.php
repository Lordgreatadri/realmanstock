<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        .summary { margin-bottom: 20px; }
        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 20px; }
        .summary-box { border: 1px solid #ddd; padding: 10px; border-radius: 5px; }
        .summary-box .label { color: #666; font-size: 10px; text-transform: uppercase; }
        .summary-box .value { font-size: 18px; font-weight: bold; color: #333; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #4F46E5; color: white; padding: 10px; text-align: left; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { margin-top: 30px; text-align: center; color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sales Report</h1>
        <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        <p>Generated: {{ \Carbon\Carbon::now()->format('M d, Y h:i A') }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-box">
                <div class="label">Total Orders</div>
                <div class="value">{{ $summary['total_orders'] }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Total Revenue</div>
                <div class="value">GHS {{ number_format($summary['total_revenue'], 2) }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Amount Paid</div>
                <div class="value">GHS {{ number_format($summary['total_paid'], 2) }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->customer->name }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td>GHS {{ number_format($order->total, 2) }}</td>
                    <td>GHS {{ number_format($order->amount_paid, 2) }}</td>
                    <td>GHS {{ number_format($order->balance, 2) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated report. No signature required.</p>
    </div>
</body>
</html>
