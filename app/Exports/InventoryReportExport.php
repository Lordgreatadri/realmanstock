<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryReportExport implements WithMultipleSheets
{
    protected $storeItems;
    protected $freezerItems;

    public function __construct($storeItems, $freezerItems)
    {
        $this->storeItems = $storeItems;
        $this->freezerItems = $freezerItems;
    }

    public function sheets(): array
    {
        return [
            new StoreItemsSheet($this->storeItems),
            new FreezerInventorySheet($this->freezerItems),
        ];
    }
}

class StoreItemsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items->map(function ($item) {
            return [
                'SKU' => $item->sku,
                'Name' => $item->name,
                'Category' => $item->category->name,
                'Quantity' => $item->quantity,
                'Unit' => $item->unit,
                'Reorder Level' => $item->reorder_level,
                'Cost Price' => $item->cost_price,
                'Selling Price' => $item->selling_price,
                'Stock Value' => $item->quantity * $item->cost_price,
                'Status' => $item->is_active ? 'Active' : 'Inactive',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Name',
            'Category',
            'Quantity',
            'Unit',
            'Reorder Level',
            'Cost Price (GHS)',
            'Selling Price (GHS)',
            'Stock Value (GHS)',
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
        return 'Store Items';
    }
}

class FreezerInventorySheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items->map(function ($item) {
            return [
                'Batch Number' => $item->batch_number,
                'Product' => $item->product_name,
                'Category' => $item->category->name,
                'Weight (kg)' => $item->weight,
                'Cost Price' => $item->cost_price,
                'Selling Price/kg' => $item->selling_price_per_kg,
                'Processing Date' => $item->processing_date->format('Y-m-d'),
                'Expiry Date' => $item->expiry_date->format('Y-m-d'),
                'Storage Location' => $item->storage_location,
                'Status' => ucfirst(str_replace('_', ' ', $item->status)),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Batch Number',
            'Product',
            'Category',
            'Weight (kg)',
            'Cost Price (GHS)',
            'Selling Price/kg (GHS)',
            'Processing Date',
            'Expiry Date',
            'Storage Location',
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
        return 'Freezer Inventory';
    }
}
