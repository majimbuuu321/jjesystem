<?php

namespace App\Filament\Resources\PurchaseOrders\Pages;

use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

     public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Purchase Order Information')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('purchase_order_no')
                                    ->label('PO No.'),

                                TextEntry::make('invoice_no')
                                    ->label('Invoice No.'),

                                TextEntry::make('received_date')
                                    ->label('Received Date')
                                    ->date('M d, Y'),

                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Draft' => 'warning',
                                        'Posted' => 'success',
                                    }),

                                TextEntry::make('warehouse.warehouse_name')
                                    ->label('Warehouse'),
                                
                                TextEntry::make('paymentTerms.payment_terms')
                                    ->label('Payment Terms'),
                            ]),
                    ]),

                Section::make('Supplier Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('supplier.company_name')
                                    ->label('Supplier'),
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
           ->visible(function ($record) {
                    return $record->status != 'Posted';
            }),

        ];
    }
}
