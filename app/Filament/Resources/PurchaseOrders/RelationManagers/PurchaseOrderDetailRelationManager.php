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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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

                                $set('unit_cost', null);
                                $set('weight', null);
                                $set('tag_weight', null);
                                $set('quantity', null);
                                $set('total_cost', null);
                                $set('net_amount', null);
                                $set('uom_id', null);

                                $product = Products::find($state);
                                if ($product) {
                                    $set('unit_cost', $product->unit_cost);
                                    $set('weight', $product->weight);
                                } else {
                                    $set('unit_cost', null);
                                    $set('weight', null);
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

                                        // Calculate tag weight
                                        if ($get('weight') == null || $get('weight') == 0) {
                                            $set('weight', null);
                                            $set('tag_weight', null);
                                        } else {
                                            $set(
                                                'tag_weight',
                                                $state * $get('weight')
                                            );
                                        }
                                    }),
                        ]),

                Grid::make(2)
                ->schema([
                    
                    TextInput::make('weight')
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
                    
                    TextInput::make('tag_weight')
                        ->numeric()
                        ->inputMode('decimal')
                        ->required()
                        ->readonly()
                        ->label('Total Weight (kg)'),
                    
                ]),
                Grid::make(2)
                ->schema([

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
                    ->label('Discount')
                    ->placeholder('e.g. -100-10% or -10%-5%')
                    ->live()
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {

                        $totalCost = (float) ($get('total_cost') ?? 0);

                        if (blank($state)) {
                            $set('discount_amount', 0);
                            $set('net_amount', $totalCost);

                            return;
                        }

                        $expression = trim($state);

                        // Split by "-"
                        $discounts = preg_split('/\s*-\s*/', ltrim($expression, '-'));

                        $amount = $totalCost;

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

                        $discountAmount = $totalCost - $amount;

                        $set('discount_amount', round($discountAmount,2));
                        $set('net_amount', round($amount,2));
                    }),

                    // TextInput::make('discount_amount')
                    //     ->numeric()
                    //     ->inputMode('decimal')
                    //     ->live(onBlur:true)
                    //     ->label('Discount Amount')
                    //     ->afterStateUpdated(function ($state, callable $set, callable $get)
                    //     {
                    //         if($get('total_cost') == null || $get('total_cost') == 0){
                    //             $set('discount_amount', null);
                    //             $set('net_amount', null);
                    //         }
                    //         else{
                    //             $set('net_amount', $get('total_cost') - $state);
                    //             // $set('discount_amount', $state / 100 * $get('total_cost'));
                    //         }
                    //     }),

                    TextInput::make('discount_amount')
                    ->label('Discount Amount')
                    ->readonly(),

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
                    ->label('Total Weight (kg)'),

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
                ->createAnother(false)
                ->hidden(fn ($livewire) => $livewire->ownerRecord->status !== 'Draft')
                ->modalHeading('Purchase Order Detail'),
            ])
            ->recordActions([
                EditAction::make()
                ->hidden(fn ($livewire) => $livewire->ownerRecord->status !== 'Draft'),
                DeleteAction::make()
                    ->modalHeading('Delete Product')
                    ->hidden(fn ($livewire) => $livewire->ownerRecord->status !== 'Draft')
                    ->modalDescription(fn ($record) =>
                        "Are you sure you want to delete {$record->product->product_description}?"
                    )
                    ->modalSubmitActionLabel('Delete'),
            ])
            ->toolbarActions([
              
            ]);
    }
}
