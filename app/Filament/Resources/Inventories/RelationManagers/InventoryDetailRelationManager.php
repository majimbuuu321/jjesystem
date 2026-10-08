<?php

namespace App\Filament\Resources\Inventories\RelationManagers;
use App\Models\InventoryPerWarehouse;
use App\Models\Products;
use App\Models\InventoryHeader;
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
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Forms\Components\Hidden;
use App\Rules\QuantityDoesNotExceedStock;
use Filament\Schemas\Components\Utilities\Get;
class InventoryDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'InventoryDetail';
    protected static ?string $title = 'Inventory Details';
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
                                            $warehouseId = $header->transfer_from;
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

                                             // Reset quantity whenever product changes
                                            $set('quantity', null);
                                            $set('weight', null);
                                            $set('gross_amount', null);

                                            $product = Products::find($state);
                                            if ($product) {
                                                $headerRecord = $livewire->ownerRecord;
                                                $inventoryPerWarehouse = InventoryPerWarehouse::where('warehouse_id', $headerRecord->transfer_from)->where('product_id', $product->id);

                                                $uomId = UnitOfMeasurement::where('unit_code', $inventoryPerWarehouse->first()->unit_code);
                                                $set('uom_id', $uomId->first()->id);
                
                                                $set('unit_of_measurement', $inventoryPerWarehouse->first()->unit_code);
                                                $set('stock_in_warehouse', $inventoryPerWarehouse->first()->quantity);
                                                $set('unit_cost', $product->unit_cost);
                                                $set('tag_weight', $product->weight);
                                            } else {
                                                $set('unit_cost', null);
                                                $set('tag_weight', null);
                                                $set('uom_id', null);
                                                $set('unit_of_measurement', null);
                                                $set('stock_in_warehouse', null);
                                                $set('weight', null);
                                                $set('gross_amount', null);
                                            }
                                            
                                        }),

                                        TextInput::make('stock_in_warehouse')
                                ->label('Stock in Warehouse')
                                ->readonly(),

                            ]),
                            Grid::make(3)
                            ->schema([

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
                                            if($get('unit_cost') == null || $get('unit_cost') == 0){
                                            $set('unit_cost', null);
                                            }
                                            else{
                                                $set('gross_amount', round($state * $get('unit_cost'),2));
                                            }
                                            // Calculate tag weight
                                            if ($get('tag_weight') == null || $get('tag_weight') == 0) {
                                                $set('tag_weight', null);
                                                $set('weight', null);
                                            } else {
                                                $set(
                                                    'weight',
                                                    $state * $get('tag_weight')
                                                );
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

                                Hidden::make('uom_id'),

                                TextInput::make('unit_of_measurement')
                                ->label('Unit of Measurement')
                                ->readonly(),




                                TextInput::make('tag_weight')
                                ->numeric()
                                ->inputMode('decimal')
                                ->required()
                                ->readonly()
                                ->label('Weight (kg)')
                                ->afterStateHydrated(function (TextInput $component, $state, $record) {
                                    if (blank($state)) {
                                        $component->state($record?->product?->weight);
                                    }
                                }),
                                    
                            ]),
                            Grid::make(3)
                            ->schema([

                                 TextInput::make('weight')
                                ->numeric()
                                ->inputMode('decimal')
                                ->required()
                                ->readonly()
                                ->label('Total Weight (kg)'),

                                TextInput::make('unit_cost')
                                    ->numeric()
                                    ->live(onBlur: true)
                                    ->minValue(0)
                                    ->label('Unit Cost')
                                    ->readonly(),
                                    // ->afterStateUpdated(function ($state, callable $set, callable $get)
                                    // {
                                    //     if($get('quantity') == null || $get('quantity') == 0){
                                    //         $set('quantity', null);
                                    //     }
                                    //     else{
                                    //         $set('total_cost', $state * $get('quantity'));
                                    //     }
                                    // }),

                                    TextInput::make('gross_amount')
                                        ->numeric()
                                        ->inputMode('decimal')
                                        ->label('Gross Amount')
                                        ->readonly(),
                            ]),

                            Grid::make(1)
                            ->schema([
                                TextArea::make('remarks')
                                    ->label('Remarks')
                                    ->dehydrateStateUsing(function (?string $state): ?string {
                                        if ($state === null) {
                                                return '';
                                                }
                                            return strtoupper($state);
                                    }),
                            ])
                    ])->columnSpanFull()

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                 TextColumn::make('product.product_code')
                    ->label('Product Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product.product_description')
                    ->label('Product Description')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('weight')
                    ->label('Weight (kg)'),

                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->sortable(),
                TextColumn::make('unit_cost')
                    ->label('Unit Cost')
                    ->money('PHP', true)
                    ->sortable(),

                TextColumn::make('gross_amount')
                    ->label('Gross Amount')
                    ->money('PHP', true)
                    ->sortable(),
                TextColumn::make('remarks')
                    ->label('Remarks'),

                TextColumn::make('gross_amount')
                    ->summarize(Sum::make()
                    ->label('Total Gross Amount')
                    ->money('PHP', true),
            ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                ->label('Add Inventory')
                ->createAnother(false)
                ->hidden(fn ($livewire) => $livewire->ownerRecord->status !== 'Draft')
                ->modalHeading('Inventory Detail'),
            ])
            ->recordActions([
                EditAction::make()
                ->mutateRecordDataUsing(function (array $data): array {

                    $getTransferFrom = InventoryHeader::where('inventory_header.id', $data['inventory_header_id'])
                    ->join('inventory_type', 'inventory_header.inventory_type_id', '=', 'inventory_type.id')
                    ->select('inventory_type.inventory_from', 'inventory_header.transfer_from')
                    ->get();
                    
                    if($getTransferFrom->first()->inventory_from == "WAREHOUSE")
                    {
                        $getStockInWarehouse = InventoryPerWarehouse::where('warehouse_id', $getTransferFrom->first()->transfer_from);
                        $data['stock_in_warehouse'] = $getStockInWarehouse->first()->quantity;
                    }
                    $uomName = UnitOfMeasurement::where('id', $data['uom_id']);
                    $data['unit_of_measurement'] = $uomName->first()->unit_code;
                    
                    return $data;
                })
                ->hidden(fn ($livewire) => $livewire->ownerRecord->status !== 'Draft'),
                DeleteAction::make()
                ->hidden(fn ($livewire) => $livewire->ownerRecord->status !== 'Draft'),
            ])
            ->toolbarActions([
              
            ]);
    }
}
