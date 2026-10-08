<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\InventoryPerWarehouse;
use Filament\Tables\Columns\TextColumn;
class LowStockAlerts extends TableWidget
{
    protected static ?string $heading = 'Low / Out of Stock';

    protected static ?int $sort = 7;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                InventoryPerWarehouse::query()
                    ->with([
                        'product',
                        'warehouse',
                    ])
                    ->where('quantity', '<=', 10)
                    ->orderBy('quantity')
            )
            ->columns([
                TextColumn::make('product.product_code')
                    ->label('Product Code'),

               TextColumn::make('product.product_description')
                    ->label('Product')
                    ->searchable(),

                TextColumn::make('warehouse.warehouse_name')
                    ->label('Warehouse'),

                TextColumn::make('quantity')
                    ->label('Stock')
                    ->numeric(decimalPlaces: 2)
                    ->badge()
                    ->color(fn ($state) =>
                        $state <= 0
                            ? 'danger'
                            : 'warning'
                    ),

                TextColumn::make('unit_code')
                    ->label('UOM'),
            ])
            ->paginated(false);
    }
}
