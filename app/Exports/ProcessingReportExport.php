<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProcessingReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $requests;
    protected $startDate;
    protected $endDate;

    public function __construct($requests, $startDate, $endDate)
    {
        $this->requests = $requests;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->requests->map(function ($request) {
            $dressingPercentage = $request->live_weight > 0 
                ? ($request->dressed_weight / $request->live_weight * 100) 
                : 0;

            return [
                'Request ID' => $request->id,
                'Customer' => $request->customer->name,
                'Animal Type' => $request->animal->animal_type,
                'Live Weight' => $request->live_weight,
                'Dressed Weight' => $request->dressed_weight,
                'Dressing %' => round($dressingPercentage, 2),
                'Processing Fee' => $request->processing_fee,
                'Processing Date' => $request->processing_date?->format('Y-m-d'),
                'Status' => ucfirst(str_replace('_', ' ', $request->status)),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Request ID',
            'Customer',
            'Animal Type',
            'Live Weight (kg)',
            'Dressed Weight (kg)',
            'Dressing %',
            'Processing Fee (GHS)',
            'Processing Date',
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
        return 'Processing Report';
    }
}
