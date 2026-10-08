<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\InvoiceDetail;
use Filament\Tables\Columns\TextColumn;
class TopSellingProducts extends TableWidget
{

    protected static ?string $heading = 'Top Selling Products';

    protected static ?int $sort = 6;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                InvoiceDetail::query()
                    ->whereHas('invoiceHeader', function (Builder $query) {
                        $query
                            ->whereIn('status', ['Paid', 'Partially Paid'])
                            ->whereBetween('invoice_date', [
                                now()->startOfMonth(),
                                now()->endOfMonth(),
                            ]);
                    })
                    ->with('product')
                    ->selectRaw('products_id as id')
                    ->selectRaw('products_id')
                    ->selectRaw('SUM(quantity) as total_quantity')
                    ->selectRaw('SUM(net_amount) as total_sales')
                    ->groupBy('products_id')
                    ->orderByDesc('total_sales')
            )
            ->columns([
                TextColumn::make('product.product_code')
                    ->label('Product Code'),

                TextColumn::make('product.product_description')
                    ->label('Product')
                    ->searchable(),

                TextColumn::make('total_quantity')
                    ->label('Qty Sold')
                    ->numeric(decimalPlaces: 2),

                TextColumn::make('total_sales')
                    ->label('Sales')
                    ->money('PHP'),
            ])
            ->paginated(false);
    }
}
