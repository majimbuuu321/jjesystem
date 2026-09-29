<?php

namespace App\Filament\Resources\PurchaseOrders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
class PurchaseOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('purchase_order_no')
                    ->label('Purchase Order No.')
                    ->sortable(),
                TextColumn::make('invoice_no')
                    ->label('Invoice No.')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('received_date')
                    ->label('Received Date')
                    ->sortable(),
                TextColumn::make('supplier.company_name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('warehouse.warehouse_name')
                    ->label('Warehouse')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        'Draft' => 'warning',
                        'Posted' => 'success',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
