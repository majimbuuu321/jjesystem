<?php

namespace App\Filament\Resources\PaymentTerms;

use App\Filament\Resources\PaymentTerms\Pages\ManagePaymentTerms;
use App\Models\PaymentTerms;
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
class PaymentTermsResource extends Resource
{
    protected static ?string $model = PaymentTerms::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingStorefront;
    protected static string | UnitEnum | null $navigationGroup = 'Product Management';
    protected static ?string $navigationLabel = 'Payment Term';
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                 TextInput::make('payment_terms')
                    ->required()
                    ->unique(ignoreRecord:true)
                    ->label('Payment Terms')
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
                TextColumn::make('payment_terms')
                    ->label('Payment Terms')
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
            'index' => ManagePaymentTerms::route('/'),
        ];
    }
}
