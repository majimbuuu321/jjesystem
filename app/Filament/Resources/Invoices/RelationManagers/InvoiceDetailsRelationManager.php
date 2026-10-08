<?php

namespace App\Filament\Resources\Invoices\RelationManagers;
use App\Models\PricePerCode;
use App\Models\Products;
use App\Models\InventoryPerWarehouse;
use App\Models\UnitOfMeasurement;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use App\Rules\QuantityDoesNotExceedStock;
use Filament\Forms\Components\Hidden;
class InvoiceDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'InvoiceDetails';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                  Section::make()
                    ->schema([
                         Grid::make(2)
                         ->schema([
                                Select::make('products_id')
                                    ->options(function (RelationManager $livewire, callable $get) {
                                        $header = $livewire->ownerRecord; // parent record
                                        $warehouseId = $header->warehouse_id;
                                        return InventoryPerWarehouse::where('inventory_per_warehouse.warehouse_id', $warehouseId)
                                            ->join('products', 'inventory_per_warehouse.product_id', '=', 'products.id')
                                            ->pluck('products.product_description', 'products.id');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->label('Product')
                                    ->loadingMessage('Loading Products...')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, RelationManager $livewire) {
                                        $product = Products::find($state);
                                        if ($product) {
                                            $headerRecord = $livewire->ownerRecord;
                                            $inventoryPerWarehouse = InventoryPerWarehouse::where('warehouse_id', $headerRecord->warehouse_id)->where('product_id', $product->id);
                                            $set('stock_in_warehouse', $inventoryPerWarehouse->first()->quantity);
                                            $invoice = $this->getOwnerRecord();
                                            // Get price code from Invoice Header -> Customer
                                            $priceCodeId = $invoice->customer?->price_code_id;

                                            $price = PricePerCode::query()
                                                ->where('products_id', $state)
                                                ->where('price_code_id', $priceCodeId)
                                                ->first();
                                            
                                            $uom = UnitOfMeasurement::find($price->units_id);

                                            $set('tag_weight', $product->weight);
                                            $set('price', $price->unit_price ?? 0);
                                            $set('unit_code', $uom?->unit_code);
                                            $set('uom_id', $price->units_id);
                                        } else {
                                            $set('stock_in_warehouse', null);
                                            $set('tag_weight', null);
                                            $set('net_weight', null);
                                            $set('quantity', null);
                                            $set('gross_amount', null);
                                            $set('price', null);
                                            $set('net_amount', null);
                                            $set('discount_rate', null);
                                            $set('discount_amount', null);
                                            $set('remarks', null);
                                            $set('unit_code', null);
                                            $set('uom_id', null);
                                        }
                                        
                                    }),

                                

                                TextInput::make('stock_in_warehouse')
                                    ->label('Available Stock')
                                    ->numeric()
                                    ->readOnly()
                                    ->dehydrated(false),

                                
                         ]),

                         Grid::make(3)
                         ->schema([
                                Select::make('price_code_id')
                                    ->label('Price Code')
                                    ->relationship('priceCode', 'price_code')
                                    ->default(function () {
                                        $invoice = $this->getOwnerRecord();

                                        return $invoice?->customer?->price_code_id;
                                    })
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),

                                TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->live(onBlur: true)
                                    ->minValue(0)
                                    ->label('Quantity')
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {

                                        $stockInWarehouse = (float) ($get('stock_in_warehouse') ?? 0);

                                        if ($state <= $stockInWarehouse) {

                                            $price = (float) ($get('price') ?? 0);

                                            // Gross Amount
                                            if ($price <= 0) {
                                                $set('price', null);
                                                $set('gross_amount', null);
                                                $set('net_amount', null);
                                            } else {

                                                $grossAmount = round($state * $price, 2);

                                                $set('gross_amount', $grossAmount);

                                                // Default Net Amount = Gross Amount
                                                $netAmount = $grossAmount;

                                                $discountRate = trim((string) ($get('discount_rate') ?? ''));

                                                if (! blank($discountRate)) {

                                                    $amount = $grossAmount;

                                                    $discounts = preg_split(
                                                        '/\s*-\s*/',
                                                        ltrim($discountRate, '-')
                                                    );

                                                    foreach ($discounts as $discount) {

                                                        $discount = trim($discount);

                                                        if ($discount === '') {
                                                            continue;
                                                        }

                                                        if (str_ends_with($discount, '%')) {

                                                            $rate = (float) str_replace('%', '', $discount);

                                                            $discountAmount = $amount * ($rate / 100);

                                                        } else {

                                                            $discountAmount = (float) $discount;
                                                        }

                                                        $amount -= $discountAmount;
                                                    }

                                                    $netAmount = round($amount, 2);
                                                }

                                                $set('net_amount', $netAmount);
                                            }

                                            // Net Weight
                                            $tagWeight = (float) ($get('tag_weight') ?? 0);

                                            if ($tagWeight <= 0) {
                                                $set('tag_weight', null);
                                                $set('net_weight', null);
                                            } else {
                                                $set(
                                                    'net_weight',
                                                    round($state * $tagWeight, 2)
                                                );
                                            }
                                        }
                                    })
                                    ->rules([
                                        fn (Get $get) => new QuantityDoesNotExceedStock(
                                            $get('stock_in_warehouse') ?? 0
                                        ),
                                    ]),


                                Hidden::make('uom_id')
                                ->dehydrated(),
                                TextInput::make('unit_code')
                                    ->label('Unit of Measurement')
                                    ->readonly(),
                         ]),

                         Grid::make(2)
                         ->schema([
                                TextInput::make('tag_weight')
                                    ->label('Weight (kg)')
                                    ->readonly(),

                                TextInput::make('net_weight')
                                    ->label('Total Weight (kg)')
                                    ->readonly(),

                               
                         ]),

                         Grid::make(2)
                         ->schema([
                                TextInput::make('price')
                                    ->numeric()
                                    ->readonly(),

                                TextInput::make('gross_amount')
                                    ->label('Gross Amount')
                                    ->readonly(),

                               
                         ]),

                          Grid::make(3)
                         ->schema([
                                TextInput::make('discount_rate')
                                ->label('Discount')
                                ->placeholder('e.g. -100-10% or -10%-5%')
                                ->live()
                                ->afterStateUpdated(function ($state, Get $get, Set $set) {

                                    $grossAmount = (float) ($get('gross_amount') ?? 0);

                                    if (blank($state)) {
                                        $set('discount_amount', 0);
                                        $set('net_amount', $grossAmount);

                                        return;
                                    }

                                    $expression = trim($state);

                                    // Split by "-"
                                    $discounts = preg_split('/\s*-\s*/', ltrim($expression, '-'));

                                    $amount = $grossAmount;

                                    foreach ($discounts as $discount) {

                                        $discount = trim($discount);

                                        if ($discount === '') {
                                            continue;
                                        }

                                        // Percentage discount
                                        if (str_ends_with($discount, '%')) {

                                            $rate = (float) str_replace('%', '', $discount);

                                            $discountAmount = $amount * ($rate / 100);

                                        } else {

                                            // Fixed amount discount
                                            $discountAmount = (float) $discount;
                                        }

                                        // Apply discount
                                        $amount -= $discountAmount;
                                    }

                                    $discountAmount = $grossAmount - $amount;

                                    $set('discount_amount', $discountAmount);
                                    $set('net_amount', $amount);
                                }),

                                TextInput::make('discount_amount')
                                    ->label('Discount Amount')
                                    ->readonly(),

                                TextInput::make('net_amount')
                                    ->label('Net Amount')
                                    ->readonly(),
                         ]),
                         Grid::make(1)
                         ->schema([
                            Textarea::make('remarks'),
                         ])

                    ])->columnSpanFull()
                
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('priceCode.price_code')
                    ->label('Price Code')
                    ->searchable(),

                TextColumn::make('product.product_code')
                    ->label('Product Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product.product_description')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Qty')
                    ->sortable(),

                TextColumn::make('units.unit_code')
                    ->label('UOM')
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Price')
                    ->sortable(),
                
                TextColumn::make('net_weight')
                    ->label('Total Weight (kg)'),

                TextColumn::make('gross_amount')
                    ->label('Gross Amount')
                    ->sortable(),

                TextColumn::make('discount_rate')
                    ->label('Discount Rate'),
                
                TextColumn::make('discount_amount')
                    ->label('Discount Amount'),
                
                TextColumn::make('net_amount')
                    ->label('Net Amount'),

                 TextColumn::make('discount_amount')
                    ->summarize(Sum::make()
                    ->label('Total Discount')
                    ->money('PHP', true)
                ),

                TextColumn::make('net_amount')
                    ->summarize(Sum::make()
                    ->label('Total Net Amount')
                    ->money('PHP', true)
                ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                ->label('Add Product')
                ->createAnother(false)
                ->after(function ($record) {
                    $invoice = $this->ownerRecord;

                    $totalAmount = $invoice->InvoiceDetails()->sum('net_amount');

                    $invoice->update([
                        'total_amount' => $totalAmount,
                        'balance_amount' => $totalAmount - ($invoice->paid_amount ?? 0),
                    ]);
                })
                ->hidden(fn ($livewire) => $livewire->ownerRecord->status !== 'Draft'),
            ])
            ->recordActions([
                EditAction::make()
                ->after(function () {
                    $invoice = $this->ownerRecord;

                    $totalAmount = $invoice->InvoiceDetails()->sum('net_amount');

                    $invoice->update([
                        'total_amount' => $totalAmount,
                        'balance_amount' => $totalAmount - ($invoice->paid_amount ?? 0),
                        
                    ]);
                })
                ->hidden(fn ($livewire) => $livewire->ownerRecord->status !== 'Draft'),
                DeleteAction::make()
                ->after(function () {
                    $invoice = $this->ownerRecord;

                    $totalAmount = $invoice->InvoiceDetails()->sum('net_amount');

                    $invoice->update([
                        'total_amount' => $totalAmount,
                        'balance_amount' => $totalAmount - ($invoice->paid_amount ?? 0),
                    ]);
                })
                ->hidden(fn ($livewire) => $livewire->ownerRecord->status !== 'Draft'),
            ])
            ->toolbarActions([
                
            ]);
    }
}
