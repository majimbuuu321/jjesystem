<?php

namespace App\Filament\Resources\InvoiceTypes;

use App\Filament\Resources\InvoiceTypes\Pages\ManageInvoiceTypes;
use App\Models\InvoiceType;
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
class InvoiceTypeResource extends Resource
{
    protected static ?string $model = InvoiceType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;
    protected static string | UnitEnum | null $navigationGroup = 'Sales Management';
    protected static ?string $navigationLabel = 'Invoice Type';
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('invoice_type_code')
                    ->required()
                    ->unique(ignoreRecord:true)
                    ->label('Invoice Type Code')
                    ->dehydrateStateUsing(function (?string $state): ?string {
                            if ($state === null) {
                                    return '';
                                    }
                                return strtoupper($state);
                            }),
                TextInput::make('invoice_type_name')
                    ->required()
                    ->label('Invoice Type Name')
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_type_code')
                     ->label('Invoice Type Code')
                    ->searchable(),
                TextColumn::make('invoice_type_name')
                    ->label('Invoice Type Name')
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
                
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageInvoiceTypes::route('/'),
        ];
    }
}
