<?php

namespace App\Filament\Resources\PriceCodes;

use App\Filament\Resources\PriceCodes\Pages\ManagePriceCodes;
use App\Models\PriceCode;
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
use Filament\Forms\Components\Select;
class PriceCodeResource extends Resource
{
    protected static ?string $model = PriceCode::class;
    protected static ?int $navigationSort = 2;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;
    protected static string | UnitEnum | null $navigationGroup = 'Product Management';
    protected static ?string $navigationLabel = 'Price Code';
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('price_code')
                    ->required()
                    ->unique(ignoreRecord:true)
                    ->label('Price Code')
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
                    ->searchable(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('price_code')
                    ->label('Price Code')
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => ManagePriceCodes::route('/'),
        ];
    }
}
