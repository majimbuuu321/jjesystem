<?php

namespace App\Filament\Resources\Products\RelationManagers;
use App\Models\PriceCode;
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

class PricePerCodeRelationManager extends RelationManager
{
    protected static string $relationship = 'PricePerCode';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(2)
                         ->schema([
                            DatePicker::make('price_date')
                                ->label('Price Date')
                                ->required()
                                ->maxDate(now()),
                         ]),
                         Grid::make(3)
                         ->schema([
                            TextInput::make('unit_price')
                                ->label('Unit Price')
                                ->required(),

                            Select::make('units_id')
                                ->label('Unit of Measurement')
                                ->preload()
                                ->options(UnitOfMeasurement::where('status', 'Active')->pluck('unit_code', 'id'))
                                ->searchable()
                                ->required()
                                ->loadingMessage('Loading Unit of Measurement...'),

                            Select::make('price_code_id')
                                ->label('Price Code')
                                ->preload()
                                ->options(PriceCode::where('status', 'Active')->pluck('price_code', 'id'))
                                ->searchable()
                                ->required()
                                ->loadingMessage('Loading Price Code...'),
                         ]),
                    ])->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('priceCode.price_code')
                    ->label('Price Code')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('price_date')
                ->label('Price Date')
                ->sortable(),

                TextColumn::make('unit_price')
                ->label('Price')
                ->sortable(),

               TextColumn::make('UOM.unit_code')
                    ->label('Unit of Measurement')
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
