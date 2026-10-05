<?php

namespace App\Filament\Resources\Inventories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Torgodly\Html2Media\Actions\Html2MediaAction;
class InventoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('transfer_date')
                    ->label('Transfer Date')
                    ->sortable(),

                TextColumn::make('document_no')
                    ->label('Document No.')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('inventory_type.inventory_type')
                    ->label('Inventory Type')
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
                ViewAction::make(),
                EditAction::make()
                ->visible(function ($record) {
                    return $record->status != 'Posted';
                }),
                 Html2MediaAction::make('print')
                    ->label('Print')
                     ->color('warning')
                    ->icon('heroicon-o-printer')
                    ->filename(fn ($record) => 'JJE-INVENTORY-' . $record->id . '.pdf')
                    ->content(fn($record) => view('pdf.inventory_per_header', [
                        'record' => $record,
                        'items' => $record->InventoryDetail,
                        'products' => $record->InventoryDetail->map(fn($item) => $item->product),
                        'uom' => $record->InventoryDetail->map(fn($item) => $item->unitOfMeasurement),
                        // 'price_code' => $record->PurchaseOrderDetail->map(fn($item) => $item->priceCode),
                ])),
            ])
            ->toolbarActions([
                
            ]);
    }
}
