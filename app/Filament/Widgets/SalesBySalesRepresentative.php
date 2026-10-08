<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\InvoiceHeader;
class SalesBySalesRepresentative extends ChartWidget
{
    protected ?string $heading = 'Sales By Sales Representative';
    protected static ?int $sort = 5;

    protected function getData(): array
    {
         $sales = InvoiceHeader::query()
            ->whereIn('status', ['Paid', 'Partially Paid'])
            ->whereBetween('invoice_date', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->with('employee')
            ->get()
            ->groupBy('employee_id')
            ->map(function ($invoices) {
                return [
                    'name' => $invoices->first()->employee
                        ? $invoices->first()->employee->first_name . ' ' .
                          $invoices->first()->employee->last_name
                        : 'Unassigned',

                    'sales' => $invoices->sum('paid_amount'),
                ];
            })
            ->sortByDesc('sales')
            ->values();

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $sales
                        ->pluck('sales')
                        ->map(fn ($value) => (float) $value)
                        ->toArray(),
                ],
            ],
            'labels' => $sales
                ->pluck('name')
                ->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
