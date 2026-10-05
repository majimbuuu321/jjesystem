<?php

namespace App\Filament\Resources\Inventories\Pages;

use App\Filament\Resources\Inventories\InventoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
class ViewInventory extends ViewRecord
{
    protected static string $resource = InventoryResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Purchase Order Information')
                    ->schema([
                        Grid::make(4)
                            ->schema([

                             TextEntry::make('transfer_date')
                                    ->label('Received Date')
                                    ->date('M d, Y'),

                                TextEntry::make('document_no')
                                    ->label('Document No.'),

                                TextEntry::make('inventory_type.inventory_type')
                                    ->label('Invoice No.'),

                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Draft' => 'warning',
                                        'Posted' => 'success',
                                    }),

                                TextEntry::make('warehouseFrom.warehouse_name')
                                    ->label('Transfer From'),
                                
                                TextEntry::make('transfer_to')
                                    ->label('Transfer To')
                                    ->state(function ($record) {
                                        if ($record->inventory_type_id == 4) {
                                            return $record->supplierTo?->company_name ?? '-';
                                        }
                                        return $record->warehouseTo?->warehouse_name ?? '-';
                                    }),

                                TextEntry::make('assigned_from')
                                    ->label('Assigned From'),
                                
                                TextEntry::make('assigned_to')
                                    ->label('Assigned To'),
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
