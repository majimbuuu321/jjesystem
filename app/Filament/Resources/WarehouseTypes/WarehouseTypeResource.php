<?php

namespace App\Filament\Resources\WarehouseTypes;

use App\Filament\Resources\WarehouseTypes\Pages\ManageWarehouseTypes;
use App\Models\WarehouseType;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
class WarehouseTypeResource extends Resource
{
    protected static ?string $model = WarehouseType::class;
    protected static ?int $navigationSort = 4;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tag;
    protected static string | UnitEnum | null $navigationGroup = 'Route & Warehouse Management';
    protected static ?string $navigationLabel = 'Warehouse Type';
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('warehouse_type_name')
                    ->required()
                    ->unique(ignoreRecord:true)
                    ->label('Warehouse Type Name')
                    ->dehydrateStateUsing(function (?string $state): ?string {
                                if ($state === null) {
                                    return '';
                                    }
                                return strtoupper($state);
                            }),
                Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'Active' => 'Active',
                        'Inactive' => 'Inactive',
                    ])
                    ->searchable()
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('warehouse_type_name')
                    ->label('Warehouse Type Name')
                    ->searchable(),
                TextColumn::make('status')
                ->badge()
                ->label('Status')
                ->color(fn (string $state): string => match ($state) {
                    'Active' => 'success',
                    'Inactive' => 'danger',
                }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created Date')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Updated Date')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['updated_by'] = auth()->id();
                    return $data;
                }),
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
            'index' => ManageWarehouseTypes::route('/'),
        ];
    }
}
