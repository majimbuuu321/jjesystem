<?php

namespace App\Filament\Resources\PurchaseOrders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Torgodly\Html2Media\Actions\Html2MediaAction;
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
                ViewAction::make(),
                EditAction::make(),
                Html2MediaAction::make('print')
                    ->label('Print')
                    // ->savePdf()
                    // ->preview()
                    // ->orientation('landscape')
                    ->format('a4', 'mm')
                    ->color('warning')
                    ->icon('heroicon-o-printer')
                    ->filename(fn ($record) => 'JJE-PO-' . $record->id . '.pdf')
                    ->content(fn($record) => view('pdf.purchase_order', [
                        'record' => $record,
                        'items' => $record->PurchaseOrderDetail,
                        'products' => $record->PurchaseOrderDetail->map(fn($item) => $item->product),
                        'uom' => $record->PurchaseOrderDetail->map(fn($item) => $item->unitOfMeasurement),
                        // 'price_code' => $record->PurchaseOrderDetail->map(fn($item) => $item->priceCode),
                ])),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
