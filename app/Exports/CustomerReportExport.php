<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $customers;
    protected $startDate;
    protected $endDate;

    public function __construct($customers, $startDate, $endDate)
    {
        $this->customers = $customers;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->customers->map(function ($customer, $index) {
            return [
                'Rank' => $index + 1,
                'Customer Name' => $customer->name,
                'Phone' => $customer->phone,
                'Email' => $customer->email,
                'Total Orders' => $customer->orders_count,
                'Total Revenue' => $customer->orders_sum_total,
                'Allow Credit' => $customer->allow_credit ? 'Yes' : 'No',
                'Credit Limit' => $customer->credit_limit,
                'Status' => $customer->is_active ? 'Active' : 'Inactive',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Rank',
            'Customer Name',
            'Phone',
            'Email',
            'Total Orders',
            'Total Revenue (GHS)',
            'Allow Credit',
            'Credit Limit (GHS)',
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
        return 'Top Customers';
    }
}
