<?php

namespace App\Filament\Resources\CreditMemos\Schemas;
use App\Models\CreditMemoHeader;
use App\Models\InvoiceHeader;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
class CreditMemoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make()
                    ->schema([
                        Grid::make(3)
                        ->schema([
                            TextInput::make('credit_memo_no')
                            ->label('Credit Memo No.')
                            ->default(function () {
                                $year = now()->year;

                                $last = CreditMemoHeader::query()
                                    ->whereYear('created_at', $year)
                                    ->latest('id')
                                    ->first();

                                $next = $last
                                    ? ((int) str_replace(
                                        "CM-{$year}-",
                                        '',
                                        $last->credit_memo_no
                                    ) + 1)
                                    : 1;

                                return "CM-{$year}-{$next}";
                            })
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated(),

                            DatePicker::make('credit_memo_date')
                            ->label('Credit Memo Date')
                            ->default(now())
                            ->required(),

                            Select::make('invoice_id')
                            ->label('Reference No.')
                            ->relationship(
                                name: 'invoice',
                                titleAttribute: 'order_no'
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {

                                if (!$state) {
                                    $set('customer_id', null);
                                    return;
                                }

                                $invoice = InvoiceHeader::find($state);

                                if ($invoice) {
                                    $set('customer_id', $invoice->customer_id);
                                    $set('warehouse_id', $invoice->warehouse_id);
                                }
                            }),
                        ]),
                        Grid::make(3)
                        ->schema([
                            Select::make('credit_memo_type')
                            ->label('Credit Memo Type')
                            ->searchable()
                            ->options([
                                'Good Return' => 'Good Return',
                                'Bad Return' => 'Bad Return',
                            ])
                            ->required()
                            ->native(false),

                            Select::make('warehouse_id')
                                ->label('Warehouse')
                                ->relationship(
                                    'warehouse',
                                    'warehouse_name'
                                )
                                ->searchable()
                                ->preload()
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->native(false),

                            Select::make('customer_id')
                                ->label('Customer')
                                ->relationship('customer', 'customer_name')
                                ->getOptionLabelFromRecordUsing(function ($record) {
                                    $name = trim(
                                        ($record->first_name ?? '') . ' ' . ($record->last_name ?? '')
                                    );

                                    if (!empty($record->store_name)) {
                                        return $record->store_name . ' - ' . $name;
                                    }

                                    return $name;
                                })
                                ->searchable()
                                ->preload()
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->native(false),
                        ]),
                        

                    ])->columnSpanFull(),
            ]);
    }
}
