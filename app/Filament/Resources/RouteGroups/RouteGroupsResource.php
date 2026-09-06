<?php

namespace App\Filament\Resources\RouteGroups;

use App\Filament\Resources\RouteGroups\Pages\ManageRouteGroups;
use App\Models\RouteGroup;
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
class RouteGroupsResource extends Resource
{
    protected static ?string $model = RouteGroup::class;
    protected static ?int $navigationSort = 2;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tag;
    protected static string | UnitEnum | null $navigationGroup = 'Route & Warehouse Management';
    protected static ?string $navigationLabel = 'Route Group';
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('route_group_name')
                    ->required()
                    ->unique(ignoreRecord:true)
                    ->label('Route Group Name')
                    ->dehydrateStateUsing(function (?string $state): ?string {
                            if ($state === null) {
                                    return '';
                                    }
                                return strtoupper($state);
                            })
                    ->columnSpanFull(),
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
                TextColumn::make('route_group_name')
                    ->label('Route Group Name')
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
                EditAction::make()->mutateFormDataUsing(function (array $data): array {
                    $data['updated_by'] = auth()->id();
                    return $data;
                }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRouteGroups::route('/'),
        ];
    }
}
