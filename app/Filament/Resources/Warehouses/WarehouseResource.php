<?php

namespace App\Filament\Resources\Warehouses;

use App\Filament\Resources\Warehouses\Pages\ManageWarehouses;
use App\Models\Warehouse;
use App\Models\Employee;
use App\Models\Routes;
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
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;
    protected static ?int $navigationSort = 3;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingStorefront;
    protected static string | UnitEnum | null $navigationGroup = 'Route & Warehouse Management';
    protected static ?string $navigationLabel = 'Warehouse';
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Select::make('employee_id')
                    ->label('Sales Representative')
                    ->preload()
                    ->required()
                    ->searchable()
                    ->options(Employee::select(
                        DB::raw("CONCAT(first_name, ' ' ,last_name) AS name"), 'id')
                        ->where('status', 'Active')
                        ->pluck('name', 'id'))
                    ->loadingMessage('Loading Sales Representative...')
                    ->columnSpanFull(),

                TextInput::make('warehouse_name')
                    ->label('Warehouse Name')
                    ->required()
                    ->dehydrateStateUsing(function (?string $state): ?string {
                            if ($state === null) {
                                return '';
                                }
                            return strtoupper($state);
                        })
                    ->columnSpanFull(),

                TextArea::make('warehouse_address')
                    ->label('Warehouse Address')
                    ->required()
                    ->dehydrateStateUsing(function (?string $state): ?string {
                            if ($state === null) {
                                return '';
                                }
                            return strtoupper($state);
                        })
                    ->columnSpanFull(),

                Select::make('warehouse_type_id')
                    ->label('Warehouse Type')
                    ->preload()
                    ->options(WarehouseType::where('status', 'Active')->pluck('warehouse_type_name', 'id'))
                    ->searchable()
                    ->required()
                    ->loadingMessage('Loading Warehouse Type...'),

                Select::make('route_id')
                    ->label('Routes')
                    ->preload()
                    ->options(Routes::where('status', 'Active')->pluck('route_name', 'id'))
                    ->searchable()
                    ->required()
                    ->loadingMessage('Loading Routes...'),

                 Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'Active' => 'Active',
                        'Inactive' => 'Inactive',
                    ])
                    ->searchable()
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('employee.first_name')
                    ->formatStateUsing(function ($state, Warehouse $route) {
                        return $route->employee->first_name . ' ' . $route->employee->last_name;
                    })
                    ->label('Sales Representative')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('warehouse_name')
                    ->label('Warehouse Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('route.route_name')
                    ->label('Route Name')
                    ->sortable()
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
            'index' => ManageWarehouses::route('/'),
        ];
    }
}
