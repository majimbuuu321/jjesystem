<?php

namespace App\Filament\Resources\Invoices\Tables;
use Filament\Actions\Action;
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
                        'Draft' => 'danger',
                        'Posted' => 'primary',
                        'Partially Paid' => 'warning',
                        'Paid' => 'success',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('print')
                ->label('Print')
                ->color('warning')
                ->icon('heroicon-o-printer')
                ->hidden(fn ($record) => in_array($record->status, ['Draft', 'Posted']))
                ->url(fn ($record) => route('invoice.print', [
                    'invoice' => $record->id,
                ]))
                ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                
            ]);
    }
}
