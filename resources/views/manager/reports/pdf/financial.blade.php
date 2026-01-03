<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        .summary { margin-bottom: 20px; }
        .summary-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px; }
        .summary-box { border: 1px solid #ddd; padding: 10px; border-radius: 5px; }
        .summary-box .label { color: #666; font-size: 10px; text-transform: uppercase; }
        .summary-box .value { font-size: 16px; font-weight: bold; color: #333; margin-top: 5px; }
        .section-title { font-size: 14px; font-weight: bold; margin: 20px 0 10px 0; color: #333; border-bottom: 2px solid #4F46E5; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #4F46E5; color: white; padding: 10px; text-align: left; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { margin-top: 30px; text-align: center; color: #666; font-size: 10px; }
        .status-paid { color: #10B981; font-weight: bold; }
        .status-partial { color: #F59E0B; font-weight: bold; }
        .status-pending { color: #EF4444; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Financial Report</h1>
        <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        <p>Generated: {{ \Carbon\Carbon::now()->format('M d, Y h:i A') }}</p>
    </div>

    <div class="section-title">Revenue Summary</div>
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-box">
                <div class="label">Total Sales Revenue</div>
                <div class="value">GHS {{ number_format($revenue['total_sales'], 2) }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Amount Paid</div>
                <div class="value">GHS {{ number_format($revenue['amount_paid'], 2) }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Outstanding Balance</div>
                <div class="value">GHS {{ number_format($revenue['outstanding'], 2) }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Total Fees</div>
                <div class="value">GHS {{ number_format($revenue['processing_fees'] + $revenue['delivery_fees'], 2) }}</div>
            </div>
        </div>
    </div>

    <div class="section-title">Inventory Value</div>
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-box">
                <div class="label">Store Items Value</div>
                <div class="value">GHS {{ number_format($inventoryValue['store_items'], 2) }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Freezer Inventory Value</div>
                <div class="value">GHS {{ number_format($inventoryValue['freezer_inventory'], 2) }}</div>
            </div>
        </div>
    </div>

    <div class="section-title">Payment Status Breakdown</div>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Count</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paymentStatus as $status)
                <tr>
                    <td>
                        <span class="status-{{ $status->payment_status }}">
                            {{ ucfirst($status->payment_status) }}
                        </span>
                    </td>
                    <td>{{ $status->count }}</td>
                    <td>GHS {{ number_format($status->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated report. No signature required.</p>
    </div>
</body>
</html>

