<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $orders;
    protected $startDate;
    protected $endDate;

    public function __construct($orders, $startDate, $endDate)
    {
        $this->orders = $orders;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->orders->map(function ($order) {
            return [
                'Order ID' => $order->id,
                'Customer' => $order->customer->name,
                'Date' => $order->created_at->format('Y-m-d'),
                'Subtotal' => $order->subtotal,
                'Processing Fee' => $order->processing_fee,
                'Delivery Fee' => $order->delivery_fee,
                'Tax' => $order->tax,
                'Discount' => $order->discount,
                'Total' => $order->total,
                'Amount Paid' => $order->amount_paid,
                'Balance' => $order->balance,
                'Payment Status' => ucfirst($order->payment_status),
                'Status' => ucfirst(str_replace('_', ' ', $order->status)),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Customer',
            'Date',
            'Subtotal (GHS)',
            'Processing Fee (GHS)',
            'Delivery Fee (GHS)',
            'Tax (GHS)',
            'Discount (GHS)',
            'Total (GHS)',
            'Amount Paid (GHS)',
            'Balance (GHS)',
            'Payment Status',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Sales Report';
    }
}
