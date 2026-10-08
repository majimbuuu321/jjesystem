<?php

namespace App\Filament\Widgets;
use App\Models\InvoiceHeader;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TodaysSales extends StatsOverviewWidget
{
     protected function getStats(): array
    {
        // Outstanding Receivables
        $receivablesQuery = InvoiceHeader::query()
            ->whereIn('status', ['Paid', 'Partially Paid'])
            ->where('balance_amount', '>', 0);

        $receivables = (clone $receivablesQuery)
            ->sum('balance_amount');

        $invoiceCount = (clone $receivablesQuery)
            ->count();

        // This Month's Sales
        $sales = InvoiceHeader::query()
            ->whereIn('status', ['Paid', 'Partially Paid'])
            ->whereBetween('invoice_date', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->sum('paid_amount');

        // Last Month's Sales
        $lastMonth = InvoiceHeader::query()
            ->whereIn('status', ['Paid', 'Partially Paid'])
            ->whereBetween('invoice_date', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ])
            ->sum('paid_amount');

        $monthlyPercentage = $lastMonth > 0
            ? (($sales - $lastMonth) / $lastMonth) * 100
            : null;

        $monthlyDescription = $monthlyPercentage === null
            ? 'No sales recorded last month'
            : number_format(abs($monthlyPercentage), 1) . '% '
                . ($monthlyPercentage >= 0 ? 'increase' : 'decrease')
                . ' from last month';

        // Today's Sales
        $todaySales = InvoiceHeader::query()
            ->whereIn('status', ['Paid', 'Partially Paid'])
            ->whereDate('invoice_date', today())
            ->sum('paid_amount');

        // Yesterday's Sales
        $yesterday = InvoiceHeader::query()
            ->whereIn('status', ['Paid', 'Partially Paid'])
            ->whereDate('invoice_date', today()->subDay())
            ->sum('paid_amount');

        $dailyPercentage = $yesterday > 0
            ? (($todaySales - $yesterday) / $yesterday) * 100
            : null;

        $dailyDescription = $dailyPercentage === null
            ? 'No sales recorded yesterday'
            : number_format(abs($dailyPercentage), 1) . '% '
                . ($dailyPercentage >= 0 ? 'increase' : 'decrease')
                . ' from yesterday';

        return [
            Stat::make(
                'Outstanding Receivables',
                '₱' . number_format($receivables, 2)
            )
                ->description(
                    number_format($invoiceCount) . ' unpaid invoice(s)'
                )
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Stat::make(
                "This Month's Sales",
                '₱' . number_format($sales, 2)
            )
                ->description($monthlyDescription)
                ->descriptionIcon(
                    $monthlyPercentage === null
                        ? 'heroicon-m-minus'
                        : ($monthlyPercentage >= 0
                            ? 'heroicon-m-arrow-trending-up'
                            : 'heroicon-m-arrow-trending-down')
                )
                ->color(
                    $monthlyPercentage === null
                        ? 'gray'
                        : ($monthlyPercentage >= 0 ? 'success' : 'danger')
                ),

            Stat::make(
                "Today's Sales",
                '₱' . number_format($todaySales, 2)
            )
                ->description($dailyDescription)
                ->descriptionIcon(
                    $dailyPercentage === null
                        ? 'heroicon-m-minus'
                        : ($dailyPercentage >= 0
                            ? 'heroicon-m-arrow-trending-up'
                            : 'heroicon-m-arrow-trending-down')
                )
                ->color(
                    $dailyPercentage === null
                        ? 'gray'
                        : ($dailyPercentage >= 0 ? 'success' : 'danger')
                ),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
