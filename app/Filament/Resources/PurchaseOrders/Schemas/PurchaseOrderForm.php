<?php

namespace App\Filament\Resources\PurchaseOrders\Schemas;
use App\Models\PurchaseOrderHeader;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
class PurchaseOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('purchase_order_no')
                                ->label('PO Number')
                                ->default(function () {
                                    $year = now()->year;
                            
                                    $lastPo = PurchaseOrderHeader::where('purchase_order_no', 'like', "PO-{$year}-%")
                                        ->orderByDesc('id')
                                        ->first();
                            
                                    $counter = $lastPo
                                        ? ((int) str_replace("PO-{$year}-", '', $lastPo->po_number)) + 1
                                        : 1;
                            
                                    return "PO-{$year}-{$counter}";
                                })
                                ->readOnly()
                                ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('received_date')
                                ->required()
                                ->label('Received Date')
                                ->minDate(now()->subYears(150))
                                ->maxDate(now())
                                ->default(now()),
                                
                                TextInput::make('invoice_no')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->label('Invoice No.'),
                            ]),
                        
                        Grid::make(3)
                            ->schema([
                                Select::make('supplier_id')
                                    ->relationship('supplier', 'company_name')
                                    ->required()
                                    ->preload()
                                    ->searchable()
                                    ->label('Delivery From')
                                    ->loadingMessage('Loading Suppliers...'),
                                    
                                Select::make('warehouse_id')
                                    ->relationship('warehouse', 'warehouse_name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Delivery To')
                                    ->loadingMessage('Loading Warehouses...'),

                                Select::make('payment_terms_id')
                                    ->relationship('paymentTerms', 'payment_terms')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Payment Terms')
                                    ->loadingMessage('Loading Payment Terms...'),
                            ])

                    ])->columnSpanFull()
            ]);
    }
}
