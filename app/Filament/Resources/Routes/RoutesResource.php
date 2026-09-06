<?php

namespace App\Filament\Resources\Routes;

use App\Filament\Resources\Routes\Pages\ManageRoutes;
use App\Models\Routes;
use App\Models\Employee;
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
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
class RoutesResource extends Resource
{
    protected static ?string $model = Routes::class;
    protected static ?int $navigationSort = 1;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::GlobeAlt;
    protected static string | UnitEnum | null $navigationGroup = 'Route & Warehouse Management';
    protected static ?string $navigationLabel = 'Route';
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

                TextInput::make('route_name')
                    ->required()
                    ->label('Route Name')
                    ->unique(ignoreRecord:true)
                    ->dehydrateStateUsing(function (?string $state): ?string {
                        if ($state === null) {
                            return '';
                            }
                        return strtoupper($state);
                    })
                    ->columnSpanFull(),

                Select::make('route_group_id')
                    ->label('Route Group')
                    ->preload()
                    ->options(RouteGroup::where('status', 'Active')->pluck('route_group_name', 'id'))
                    ->searchable()
                    ->required()
                    ->loadingMessage('Loading Route Group...'),

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
                    ->formatStateUsing(function ($state, Routes $route) {
                        return $route->employee->first_name . ' ' . $route->employee->last_name;
                    })
                    ->label('Sales Representative')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('route_name')
                    ->label('Route Name')
                    ->searchable(),

                TextColumn::make('route_group.route_group_name')
                    ->searchable()
                    ->label('Route Group')
                    ->sortable(),

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
            'index' => ManageRoutes::route('/'),
        ];
    }
}
