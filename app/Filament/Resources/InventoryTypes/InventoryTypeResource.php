<?php

namespace App\Filament\Resources\InventoryTypes;

use App\Filament\Resources\InventoryTypes\Pages\ManageInventoryTypes;
use App\Models\InventoryType;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
class InventoryTypeResource extends Resource
{
    protected static ?string $model = InventoryType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tag;
    protected static string | UnitEnum | null $navigationGroup = 'Inventory Management';
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                ->schema([
                    Grid::make(3)
                    ->schema([
                        TextInput::make('inventory_type')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->label('Inventory Type')
                        ->dehydrateStateUsing(function (?string $state): ?string {
                                if ($state === null) {
                                    return '';
                                    }
                                return strtoupper($state);
                            }),
                    ]),
                    Grid::make(2)
                    ->schema([
                        Select::make('inventory_from')
                        ->searchable()
                        ->required()
                        ->options([
                            'SUPPLIER' => 'SUPPLIER',
                            'WAREHOUSE' => 'WAREHOUSE',
                        ])
                        ->label('Inventory To'),

                        Select::make('inventory_to')
                        ->searchable()
                        ->required()
                        ->options([
                            'SUPPLIER' => 'SUPPLIER',
                            'WAREHOUSE' => 'WAREHOUSE',
                        ])
                        ->label('Inventory To')
                    ]),
                    Grid::make(2)
                    ->schema([
                         Select::make('status')
                        ->label('Status')
                        ->required()
                        ->options([
                            'Active' => 'Active',
                            'Inactive' => 'Inactive',
                        ])
                        ->searchable()
                    ])
                ])->columnSpanFull()
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    TextColumn::make('inventory_type')
                        ->searchable()
                        ->label('Inventory Type'),
                    TextColumn::make('inventory_from')
                        ->label('Inventory From')
                        ->sortable(),
                    TextColumn::make('inventory_to')
                        ->label('Inventory To')
                        ->sortable(),

                    TextColumn::make('status')
                        ->badge()
                        ->label('Status')
                        ->color(fn (string $state): string => match ($state) {
                            'Active' => 'success',
                            'Inactive' => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                //DeleteAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageInventoryTypes::route('/'),
        ];
    }
}
