<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrdersExport implements FromCollection, WithHeadings, WithStyles
{
    protected $from, $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        $orders = Order::with('orderItems.product')
            ->when($this->from && $this->to, function ($q) {
                $q->whereBetween('created_at', [$this->from, $this->to]);
            })
            ->get();

        $rows = $orders->map(function ($order) {
            return [
                'ID' => $order->id,
                'Name' => $order->full_name,
                'Items' => $order->orderItems->pluck('product.name')->join(', '),
                'QTY' => $order->orderItems->sum('quantity'),
                'Phone' => $order->phone,
                'Address' => $order->address,
                'Total' => $order->total_amount,
                'Status' => $order->status,
                'Date' => $order->created_at,
            ];
        });

        // Append a totals summary row with total quantity and total amount
        $totalQty = $orders->sum(function ($o) {
            return $o->orderItems->sum('quantity');
        });
        $totalAmount = $orders->sum('total_amount');

        $rows->push([
            'ID' => '',
            'Name' => 'TOTAL',
            'Items' => '',
            'QTY' => $totalQty,
            'Phone' => '',
            'Address' => '',
            'Total' => $totalAmount,
            'Status' => '',
            'Date' => '',
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Name',
            'Items',
            'QTY',
            'Phone',
            'Address',
            'Total',
            'Status',
            'Date',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4CAF50'],
                ],
            ],
        ];
    }
}
