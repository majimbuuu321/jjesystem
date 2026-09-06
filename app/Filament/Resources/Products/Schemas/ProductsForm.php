<?php

namespace App\Filament\Resources\Products\Schemas;
use App\Models\ProductCategory;
use App\Models\Supplier;
use App\Models\Warehouse;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
class ProductsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Section::make()
                    ->schema([
                        Grid::make(3)
                         ->schema([
                            TextInput::make('product_code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->label('Product Code')
                            ->dehydrateStateUsing(function (?string $state): ?string {
                                if ($state === null) {
                                    return '';
                                }
                                    return strtoupper($state);
                            }),
                         ]),
                    
                    TextArea::make('product_description')
                        ->required()
                        ->label('Product Description')
                        ->dehydrateStateUsing(function (?string $state): ?string {
                        if ($state === null) {
                            return '';
                        }
                            return strtoupper($state);
                    }),
                     Grid::make(2)
                         ->schema([
                            Select::make('product_category_id')
                                ->label('Product Category')
                                ->options(ProductCategory::all()->pluck('product_category_name', 'id'))
                                ->searchable()
                                ->required()
                                ->loadingMessage('Loading Product Category...'),

                            DatePicker::make('price_date')
                                ->label('Price Date')
                                ->required()
                                ->maxDate(now())
                                ->default(now())
                                ->minDate(now()->subYears(150)),
                         ]),
                    
                     Grid::make(2)
                         ->schema([
                            TextInput::make('unit_cost')
                                ->numeric()
                                ->default(null)
                                ->prefix('₱'),
                            TextInput::make('unit_price')
                                ->numeric()
                                ->default(null)
                                ->prefix('₱'),
                         ]),
                    
                    
                    Select::make('supplier_id')
                        ->label('Supplier')
                        ->options(Supplier::all()->pluck('company_name', 'id'))
                        ->searchable()
                        ->required()
                        ->loadingMessage('Loading Supplier...'),

                    Select::make('warehouse_id')
                        ->label('Warehouse')
                        ->options(Warehouse::all()->pluck('warehouse_name', 'id'))
                        ->searchable()
                        ->required()
                        ->loadingMessage('Loading Warehouse...'),

                    TextInput::make('reorder_level')
                        ->label('Reorder Level')
                        ->numeric(),

                    TextInput::make('weight')
                        ->label('Weight(kg)')
                        ->numeric(),

                    Select::make('status')
                        ->label('Status')
                        ->required()
                        ->options([
                            'Active' => 'Active',
                            'Inactive' => 'Inactive',
                        ])
                        ->searchable()
                 ])->columnSpanFull()
                
            ]);
    }
}
