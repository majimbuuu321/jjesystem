<?php

namespace App\Filament\Resources\PurchaseOrders\RelationManagers;
use App\Models\Products;
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
class PurchaseOrderDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'PurchaseOrderDetail';
    protected static ?string $title = 'Purchase Order Detail';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(2)
                        ->schema([
                            Select::make('products_id')
                            ->relationship('product', 'product_description')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->label('Product')
                            ->loadingMessage('Loading Products...')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $product = Products::find($state);
                                if ($product) {
                                    $set('unit_cost', $product->unit_cost);
                                } else {
                                    $set('unit_cost', null);
                                }
                            }),
                        ]),

                        Grid::make(2)
                ->schema([

                    Select::make('uom_id')
                        ->relationship('unitOfMeasurement', 'unit_code')
                        ->required()
                        ->preload()
                        ->searchable()
                        ->label('Unit of Measurement')
                        ->loadingMessage('Loading Unit of Measurement...'),

                        TextInput::make('tag_weight')
                        ->numeric()
                        ->inputMode('decimal')
                        ->required()
                        ->label('Weight (kg)'),
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
                                if($get('unit_cost') == null || $get('unit_cost') == 0){
                                    $set('unit_cost', null);
                                }
                                else{
                                    $set('total_cost', $state * $get('unit_cost'));
                                    $set('net_amount', $get('total_cost') - $get('discount_amount'));
                                }
                            }),
                    TextInput::make('unit_cost')
                        ->numeric()
                        ->live(onBlur: true)
                        ->minValue(0)
                        ->label('Unit Cost')
                        ->readonly()
                        ->afterStateUpdated(function ($state, callable $set, callable $get)
                            {
                                if($get('quantity') == null || $get('quantity') == 0){
                                    $set('quantity', null);
                                }
                                else{
                                    $set('total_cost', $state * $get('quantity'));
                                    $set('net_amount', $get('total_cost') - $get('discount_amount'));
                                }
                            }),

                    TextInput::make('total_cost')
                        ->numeric()
                        ->inputMode('decimal')
                        ->label('Total Cost')
                        ->readonly(),
                    
                ]),

                Grid::make(3)
                ->schema([
                    TextInput::make('discount_rate')
                        ->numeric()
                        ->inputMode('decimal')
                        ->live(onBlur: true)
                        ->label('Discount Rate (%)')
                        ->afterStateUpdated(function ($state, callable $set, callable $get)
                            {
                                if($get('total_cost') == null || $get('total_cost') == 0){
                                    $set('discount_amount', null);
                                    $set('net_amount', null);
                                }
                                else{
                                    $set('net_amount', $get('total_cost') - ($state / 100 * $get('total_cost')));
                                    $set('discount_amount', $state / 100 * $get('total_cost'));
                                }
                            }),

                    TextInput::make('discount_amount')
                        ->numeric()
                        ->inputMode('decimal')
                        ->live(onBlur:true)
                        ->label('Discount Amount')
                        ->afterStateUpdated(function ($state, callable $set, callable $get)
                        {
                            if($get('total_cost') == null || $get('total_cost') == 0){
                                $set('discount_amount', null);
                                $set('net_amount', null);
                            }
                            else{
                                $set('net_amount', $get('total_cost') - $state);
                                // $set('discount_amount', $state / 100 * $get('total_cost'));
                            }
                        }),

                    TextInput::make('net_amount')
                        ->numeric()
                        ->inputMode('decimal')
                        ->label('Net Amount')
                        ->readonly(),
                ]),

                Grid::make(1)
                ->schema([
                    Textarea::make('remarks')
                        ->label('Remarks'),
                ])
                    ])->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('product.product_description')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('unitOfMeasurement.unit_code')
                    ->label('Unit of Measurement')
                    ->sortable(),
                TextColumn::make('tag_weight')
                    ->label('Weight (kg)'),

                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->sortable(),
                TextColumn::make('unit_cost')
                    ->label('Unit Cost')
                    ->money('PHP', true)
                    ->sortable(),
                TextColumn::make('total_cost')
                    ->label('Total Cost')
                    ->money('PHP', true)
                    ->sortable(),
                TextColumn::make('discount_rate')
                    ->label('Discount Rate (%)')
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->label('Discount Amount')
                    ->money('PHP', true)
                    ->sortable(),
                TextColumn::make('net_amount')
                    ->label('Net Amount')
                    ->money('PHP', true)
                    ->sortable(),
                
                TextColumn::make('total_cost')
                    ->summarize(Sum::make()
                    ->label('Gross Amount')
                    ->money('PHP', true)
                ),

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
                ->modalHeading('Purchase Order Detail'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->modalHeading('Delete Product')
                    ->modalDescription(fn ($record) =>
                        "Are you sure you want to delete {$record->product->product_description}?"
                    )
                    ->modalSubmitActionLabel('Delete'),
            ])
            ->toolbarActions([
              
            ]);
    }
}
