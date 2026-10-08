<?php

namespace App\Filament\Resources\Suppliers;

use App\Filament\Resources\Suppliers\Pages\ManageSuppliers;
use App\Models\Supplier;
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
class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice2;
    protected static string | UnitEnum | null $navigationGroup = 'Supplier Management';
    protected static ?string $navigationLabel = 'Suppliers';
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('company_name')
                                ->required()
                                ->label('Company Name')
                                ->dehydrateStateUsing(function (?string $state): ?string {
                                if ($state === null) {
                                    return '';
                                    }
                                return strtoupper($state);
                            }),
                            
                           
                        ]),
                    
                    Grid::make(3)
                        ->schema([
                            TextInput::make('first_name')
                                ->required()
                                ->label('First Name')
                                ->dehydrateStateUsing(function (?string $state): ?string {
                                if ($state === null) {
                                    return '';
                                    }
                                return strtoupper($state);
                            }),
                            TextInput::make('middle_name')
                                ->label('Middle Name')
                                ->dehydrateStateUsing(function (?string $state): ?string {
                                if ($state === null) {
                                    return '';
                                    }
                                return strtoupper($state);
                            }),
                            TextInput::make('last_name')
                                ->required()
                                ->label('Last Name')
                                ->dehydrateStateUsing(function (?string $state): ?string {
                                if ($state === null) {
                                    return '';
                                    }
                                return strtoupper($state);
                            }),
                        ]),
                    Grid::make(1)
                        ->schema([
                            Textarea::make('supplier_address')
                                ->required()
                                ->label('Supplier Address')
                                ->dehydrateStateUsing(function (?string $state): ?string {
                                if ($state === null) {
                                    return '';
                                    }
                                return strtoupper($state);
                            }),
                        ]),
                    Grid::make(3)
                        ->schema([
                            TextInput::make('email')
                                ->label('Email Address')
                                ->email(),
                            TextInput::make('contact_number')
                                ->required()
                                ->label('Contact Number')
                                ->tel()
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
                        ]),
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
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
            'index' => ManageSuppliers::route('/'),
        ];
    }
}
