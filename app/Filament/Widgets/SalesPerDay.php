<?php

namespace App\Filament\Widgets;
use App\Models\InvoiceHeader;
use Filament\Widgets\ChartWidget;

class SalesPerDay extends ChartWidget
{
    protected ?string $heading = 'Sales Per Day';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
         $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $sales = InvoiceHeader::query()
            ->whereIn('status', ['Paid', 'Partially Paid'])
            ->whereBetween('invoice_date', [$start, $end])
            ->selectRaw('DATE(invoice_date) as sale_date')
            ->selectRaw('SUM(paid_amount) as total_sales')
            ->groupByRaw('DATE(invoice_date)')
            ->orderBy('sale_date')
            ->get()
            ->keyBy('sale_date');

        $labels = [];
        $data = [];

        $date = $start->copy();

        while ($date <= $end) {
            $key = $date->format('Y-m-d');

            $labels[] = $date->format('M d');

            $data[] = isset($sales[$key])
                ? (float) $sales[$key]->total_sales
                : 0;

            $date->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $data,
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
