<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $orders;
    protected $revenue;
    protected $inventoryValue;
    protected $startDate;
    protected $endDate;

    public function __construct($orders, $revenue, $inventoryValue, $startDate, $endDate)
    {
        $this->orders = $orders;
        $this->revenue = $revenue;
        $this->inventoryValue = $inventoryValue;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $data = collect();
        
        // Summary section
        $data->push(['FINANCIAL SUMMARY', '', '', '']);
        $data->push(['Total Sales Revenue', $this->revenue['total_sales'], '', '']);
        $data->push(['Amount Paid', $this->revenue['amount_paid'], '', '']);
        $data->push(['Outstanding Balance', $this->revenue['outstanding'], '', '']);
        $data->push(['Processing Fees', $this->revenue['processing_fees'], '', '']);
        $data->push(['Delivery Fees', $this->revenue['delivery_fees'], '', '']);
        $data->push(['', '', '', '']);
        
        $data->push(['INVENTORY VALUE', '', '', '']);
        $data->push(['Store Items Value', $this->inventoryValue['store_items'], '', '']);
        $data->push(['Freezer Inventory Value', $this->inventoryValue['freezer_inventory'], '', '']);
        $data->push(['Total Inventory Value', $this->inventoryValue['store_items'] + $this->inventoryValue['freezer_inventory'], '', '']);
        $data->push(['', '', '', '']);
        
        // Order details header
        $data->push(['ORDER DETAILS', '', '', '']);
        
        // Orders data
        foreach ($this->orders as $order) {
            $paymentStatus = $order->balance == 0 ? 'Paid' : ($order->amount_paid > 0 ? 'Partial' : 'Pending');
            $data->push([
                $order->id,
                $order->customer->name,
                $order->total,
                $order->amount_paid,
                $order->balance,
                $paymentStatus,
            ]);
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Customer',
            'Total (GHS)',
            'Paid (GHS)',
            'Balance (GHS)',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            17 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Financial Report';
    }
}
