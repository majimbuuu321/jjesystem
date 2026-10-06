<?php

namespace App\Filament\Resources\Invoices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('order_no')
                    ->label('Order No.')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice_date')
                    ->label('Invoice Date')
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
                
            ]);
    }
}
