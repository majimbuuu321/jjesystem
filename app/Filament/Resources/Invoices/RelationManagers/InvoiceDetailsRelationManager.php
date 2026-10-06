<?php

namespace App\Filament\Resources\Invoices\RelationManagers;
use App\Models\PricePerCode;
use App\Models\Products;
use App\Models\InventoryPerWarehouse;
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
use App\Rules\QuantityDoesNotExceedStock;
class InvoiceDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'InvoiceDetails';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Select::make('product_id')
                // ->label('Product')
                // ->required()
                // ->searchable()
                // ->preload()
                // ->options(function () {
                //     $invoice = $this->getOwnerRecord();

                //     if (! $invoice?->warehouse_id) {
                //         return [];
                //     }
                //     $productIds = InventoryPerWarehouse::query()
                //         ->where('warehouse_id', $invoice->warehouse_id)
                //         ->where('quantity', '>', 0)
                //         ->pluck('product_id');

                //     return Products::query()
                //         ->whereIn('id', $productIds)
                //         ->orderBy('product_description')
                //         ->pluck('product_description', 'id')
                //         ->toArray();
                // })
                // ->live(),
                Select::make('products_id')
                    ->options(function (RelationManager $livewire, callable $get) {
                        $header = $livewire->ownerRecord; // parent record
                        $warehouseId = $header->warehouse_id;
                        // $transferFrom = InventoryType::where('id', $header->first()->inventory_type_id)->select('inventory_from')->get();

                        // if($transferFrom = "WAREHOUSE")
                        // {
                        //     $ware   
                        // }
                        // $warehouseId = $header->warehouse_id;
                        // dd($header);
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
                                ->value('unit_price');

                            $set('price', $price ?? 0);
                        } else {
                            $set('stock_in_warehouse', null);
                        }
                        
                    }),

                TextInput::make('stock_in_warehouse')
                ->label('Available Stock')
                ->numeric()
                ->readOnly()
                ->dehydrated(false),
               

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
                    ->afterStateUpdated(function ($state, callable $set, callable $get)
                        {
                            $stockInWarehouse = $get('stock_in_warehouse');

                            if($state <= $stockInWarehouse)
                            {
                                if($get('price') == null || $get('price') == 0){
                                $set('price', null);
                                }
                                else{
                                    $set('gross_amount', $state * $get('price'));
                                }
                            }
                            // else{
                            //     if($get('unit_cost') == null || $get('unit_cost') == 0){
                            //     $set('unit_cost', null);
                            //     }
                            //     else{
                            //         $set('gross_amount', $state * $get('unit_cost'));
                            //     }
                            // }

                            //   if($get('unit_cost') == null || $get('unit_cost') == 0){
                            //     $set('unit_cost', null);
                            //     }
                            //     else{
                            //         $set('gross_amount', $state * $get('unit_cost'));
                            //     }
                    }) 
                    ->rules([
                            fn (Get $get) => new QuantityDoesNotExceedStock($get('stock_in_warehouse') ?? 0),
                        ]),

                TextInput::make('price')
                    ->numeric()
                    ->readonly(),

                 TextInput::make('gross_amount')
                    ->readonly(),

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
                    ->readonly(),
                // TextInput::make('discount_rate')
                //     ->label('Discount Rate')
                //     ->placeholder('e.g. .5 .1 or .5 5%')
                //     ->live()
                //     ->afterStateUpdated(function ($state, Get $get, Set $set) {

                //         if (blank($state)) {
                //             $set('discount_amount', 0);
                //             return;
                //         }

                //         // Split by spaces
                //         $discounts = preg_split('/\s+/', trim($state));

                //         $total = 0;

                //         foreach ($discounts as $discount) {
                //             $discount = trim($discount);

                //             // Remove % sign
                //             $discount = str_replace('%', '', $discount);

                //             if (is_numeric($discount)) {
                //                 $total += (float) $discount;
                //             }
                //         }

                //         $amount = (float) ($get('gross_amount') ?? 0);

                //         // Apply discounts sequentially
                //         foreach ($discounts as $discount) {
                //             $discount = str_replace('%', '', trim($discount));

                //             if (is_numeric($discount)) {
                //                 $rate = (float) $discount / 100;
                //                 $amount -= $amount * $rate;
                //             }
                //         }

                //         $set('discount_amount', $total);
                //         $set('net_amount', $amount);
                //     }),

               

                TextInput::make('net_amount')
                    ->readonly(),

                Textarea::make('remarks')
                    ->columnSpanFull(),
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

                TextColumn::make('price')
                    ->label('Price')
                    ->sortable(),

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
                ->hidden(fn ($livewire) => $livewire->ownerRecord->status !== 'Draft'),
            ])
            ->recordActions([
                EditAction::make()
                ->hidden(fn ($livewire) => $livewire->ownerRecord->status !== 'Draft'),
                DeleteAction::make()
                ->hidden(fn ($livewire) => $livewire->ownerRecord->status !== 'Draft'),
            ])
            ->toolbarActions([
                
            ]);
    }
}
