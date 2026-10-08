<?php

namespace App\Filament\Resources\CreditMemos\Tables;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
class CreditMemosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('credit_memo_no')
                    ->label('Credit Memo No.')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('reference_no')
                    ->label('Reference No.')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('credit_memo_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('credit_memo_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Good Return' => 'success',
                        'Bad Return' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('customer_id')
                ->label('Customer')
                ->formatStateUsing(function ($state, $record) {
                    $customer = $record->customer;

                    if (!$customer) {
                        return '—';
                    }

                    $name = trim(
                        ($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')
                    );

                    return !empty($customer->store_name)
                        ? $customer->store_name . ' - ' . $name
                        : $name;
                })
                ->searchable(),
                
                TextColumn::make('warehouse.warehouse_name')
                    ->label('Warehouse')
                    ->searchable(),
                
                TextColumn::make('status')
                    ->badge()
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
                Action::make('print')
                    ->label('Print')
                    ->color('warning')
                    ->icon('heroicon-o-printer')
                    // ->hidden(fn ($record) => in_array($record->status, ['Draft', 'Posted']))
                    ->url(fn ($record) => route('credit-memo.print', [
                        'creditMemo' => $record->id,
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                
            ]);
    }
}
